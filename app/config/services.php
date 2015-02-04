<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as FlashManager;


/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
        $url = new UrlResolver();
        $url->setBaseUri($config->application->baseUri);

        return $url;
    }, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

        $view = new View();

        $view->setViewsDir($config->application->viewsDir);

        $view->registerEngines(array(
                '.volt' => function ($view, $di) use ($config) {

                        $volt = new VoltEngine($view, $di);

                        $volt->setOptions(array(
                                'compiledPath' => $config->application->cacheDir,
                                'compiledSeparator' => '_'
                            ));

                        return $volt;
                    },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ));

        return $view;
    }, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
        return new DbAdapter(array(
            'host' => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname' => $config->database->dbname
        ));
    });

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
        return new MetaDataAdapter();
    });

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
        $session = new SessionAdapter();
        $session->start();

        return $session;
    });

/**
 * Start router
 */
$di->set('router', function(){
        return require "router.php";

    });

/**
 * Set security component
 */
$di->set('security', function(){

        $security = new Phalcon\Security();

        //Set the password hashing factor to 12 rounds
        $security->setWorkFactor(12);

        return $security;
    }, true);

/**
 * Dispatcher use default namespace
 */

$di->set('dispatcher', function() {
        //  Create/Get an EventManager
        $eventsManager = new Phalcon\Events\Manager();

        //Attach a listener
        $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {


                //Alternative way, controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(array(
                                    'controller' => 'error',
                                    'action' => 'notfound'
                                ));
                            return false;
                    }
                }
            });

        $dispatcher = new Dispatcher();

        //Bind the EventsManager to the dispatcher
        $dispatcher->setEventsManager($eventsManager);
        $dispatcher->setDefaultNamespace('App\Controllers');
        return $dispatcher;

    });

//Set up the flash service
$di->set('flash', function() {
        return new FlashManager();
    });

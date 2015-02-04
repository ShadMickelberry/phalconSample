<?php

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(
    array(
        'App\Controllers' => $config->application->controllersDir,
        'App\Models'      => $config->application->modelsDir,
        'App\Library'         => $config->application->libraryDir
    )
);

$loader->registerDirs(
    array(
        __DIR__ . '/../../app/libarary',

    ));
$loader->register();

//require composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir
    )
)->register();

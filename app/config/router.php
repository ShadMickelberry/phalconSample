<?php
/**
 * Created by PhpStorm.
 * User: shad
 * Date: 12/19/14
 * Time: 10:22 AM
 */

    $router = new \Phalcon\Mvc\Router();



$router->add(
        "/sitemap",
            array(
             "controller" => 'sitemap',
             "action"     => 'index'
            )
);


    $router->handle();
    return $router;

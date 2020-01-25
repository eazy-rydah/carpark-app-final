<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */ 
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('signup', ['controller' => 'Signup', 'action' => 'show']);
$router->add('login', ['controller' => 'Login', 'action' => 'show']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('admin', ['controller' => 'Admin', 'action' => 'index']);
$router->add('password/show-reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'show-reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('{controller}/{id:\d+}/{action}/{ud:\d+}');
    
$router->dispatch($_SERVER['QUERY_STRING']);

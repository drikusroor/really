<?php

// These lines are for DEVELOPMENT only.  You should never display errors
// in a production environment.
error_reporting(E_ALL);
ini_set( 'display_errors','1');

include 'autoloader.php';

$router = new Router(
    [
        new Route('/', 'HomeController@index'),
        new Route('/about', 'HomeController@about'),
    ]
);

$router->execute($_SERVER['REQUEST_URI']);

// echo a basic html page
echo '<html><head><title>PHP Test</title></head><body><h1>PHP Test</h1></body></html>';

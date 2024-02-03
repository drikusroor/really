<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Ainab\Really\DI;
use Ainab\Really\Router;
use Ainab\Really\Route;

$container = new DI\Container();
$container->bind('ManagePageService', 'Ainab\Really\Service\ManagePageService');
$container->bind('BaseController', 'Ainab\Really\Controller\BaseController');
$container->bind('HomeController', 'Ainab\Really\Controller\HomeController');
$container->bind('AdminHomeController', 'Ainab\Really\Controller\Admin\AdminHomeController');
$container->bind('ErrorController', 'Ainab\Really\Controller\ErrorController');

// These lines are for DEVELOPMENT only.  You should never display errors
// in a production environment.
error_reporting(E_ALL);
ini_set( 'display_errors','1');

$router = new Router(
    [
        new Route('/', 'HomeController@index'),
        new Route('/about', 'HomeController@about'),
    ]
);

$router->group(['prefix' => 'admin'], function ($router) {
    $router->addRoute(new Route('/', 'AdminHomeController@index'));
    $router->addRoute(new Route('/post', 'AdminHomeController@post'));
});

$router->execute($_SERVER['REQUEST_URI'], $container);

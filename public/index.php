<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Ainab\Really\DI;
use Ainab\Really\Router;
use Ainab\Really\Route;


$container = new DI\Container();
$container->bind('ManageContentService', 'Ainab\Really\Service\ManageContentService');
$container->bind('BaseController', 'Ainab\Really\Controller\BaseController');
$container->bind('AdminHomeController', 'Ainab\Really\Controller\Admin\AdminHomeController');
$container->bind('AdminContentController', 'Ainab\Really\Controller\Admin\AdminContentController');
$container->bind('ErrorController', 'Ainab\Really\Controller\ErrorController');

// These lines are for DEVELOPMENT only.  You should never display errors
// in a production environment.
error_reporting(E_ALL);
ini_set( 'display_errors','1');

$router = new Router(
    [
        new Route('/about', 'HomeController@about'),
        ]
    );
    
    $router->group(['prefix' => 'admin'], function ($router) {
        $router->addRoute(new Route('/', 'AdminHomeController@index'));
        $router->addRoute(new Route('/pages', 'AdminContentController@index'));
        $router->addRoute(new Route('/pages/save', 'AdminContentController@save'));
        $router->addRoute(new Route('/pages/edit', 'AdminContentController@edit'));
        $router->addRoute(new Route('/pages/delete', 'AdminContentController@delete'));
        $router->addRoute(new Route('/pages/rebuild', 'AdminContentController@rebuild'));
        $router->addRoute(new Route('/pages/preview', 'AdminContentController@preview'));
        $router->addRoute(new Route('/not-found', 'ErrorController@notFound'));
    });
    
$router->execute($_SERVER['REQUEST_URI'], $container);

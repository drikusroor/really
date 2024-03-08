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
$container->bind('AdminPageController', 'Ainab\Really\Controller\Admin\AdminPageController');
$container->bind('AdminPostController', 'Ainab\Really\Controller\Admin\AdminPostController');
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

        $router->addRoute(new Route('/pages', 'AdminPageController@index'));
        $router->addRoute(new Route('/pages/save', 'AdminPageController@save'));
        $router->addRoute(new Route('/pages/edit', 'AdminPageController@edit'));
        $router->addRoute(new Route('/pages/delete', 'AdminPageController@delete'));
        $router->addRoute(new Route('/pages/rebuild', 'AdminPageController@rebuild'));
        $router->addRoute(new Route('/pages/preview', 'AdminPageController@preview'));

        $router->addRoute(new Route('/posts', 'AdminPostController@index'));
        $router->addRoute(new Route('/posts/save', 'AdminPostController@save'));
        $router->addRoute(new Route('/posts/edit', 'AdminPostController@edit'));
        $router->addRoute(new Route('/posts/delete', 'AdminPostController@delete'));
        $router->addRoute(new Route('/posts/rebuild', 'AdminPostController@rebuild'));
        $router->addRoute(new Route('/posts/preview', 'AdminPostController@preview'));
        
        $router->addRoute(new Route('/not-found', 'ErrorController@notFound'));
    });
    
$router->execute($_SERVER['REQUEST_URI'], $container);

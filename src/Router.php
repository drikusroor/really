<?php

namespace Ainab\Really;

use Ainab\Really\Controller\ErrorController;
use Ainab\Really\DI\Container;

class Router
{
    private $routes = [];
    private $currentGroupOptions = [];
    private $middlewares = [];


    /**
     * Constructor
     *
     * @param Route[] $routes An array of routes to be added to the router.
     */
    public function __construct(array $routes = [])
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    /**
     * Adds a route to the router.
     *
     * @param Route $route The route to be added.
     */
    public function addRoute(Route $route)
    {
        if (isset($this->currentGroupOptions['prefix'])) {
            $route->prependPath($this->currentGroupOptions['prefix']);
        }
        if (isset($this->currentGroupOptions['middleware'])) {
            $route->setMiddlewares($this->currentGroupOptions['middleware']);
        }

        $this->routes[] = $route;
    }

    public function group($options, $routesClosure)
    {
        $previousOptions = $this->currentGroupOptions;
        $this->currentGroupOptions = $options; // Store current group options

        call_user_func($routesClosure, $this); // Define routes within the closure

        $this->currentGroupOptions = $previousOptions; // Revert to previous group options
    }

    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }
    /**
     * Executes the route matching the given url.
     *
     * @param string $url The url to be matched.
     */
    public function execute($url, Container $container)
    {
        $request = $container->make('Request');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route->matches($url, $method)) {
                foreach ($route->getMiddlewares() as $middleware) {
                    $middlewareInstance = $container->make($middleware);
                    $request = $container->make('Request');
                    $middlewareInstance->handle($request);
                }
                return $route->execute($container);
            }
        }

        return $this->notFound($container);
    }

    private function notFound($container)
    {
        $controller = new ErrorController();
        return $controller->notFound();
    }
}

<?php

namespace Ainab\Really;

class Router
{

    private $routes = [];
    private $currentGroupOptions = [];


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

        $this->routes[] = $route;
    }

    public function group($options, $routesClosure)
    {
        $previousOptions = $this->currentGroupOptions;
        $this->currentGroupOptions = $options; // Store current group options

        call_user_func($routesClosure, $this); // Define routes within the closure

        $this->currentGroupOptions = $previousOptions; // Revert to previous group options
    }

    /**
     * Executes the route matching the given url.
     *
     * @param string $url The url to be matched.
     */
    public function execute($url, $container)
    {
        foreach ($this->routes as $route) {
            if ($route->matches($url)) {
                return $route->execute($container);
            }
        }
    }
}

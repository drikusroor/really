<?php

class Router {

    private $routes = [];


    /**
     * Constructor
     *
     * @param Route[] $routes An array of routes to be added to the router.
     */
    public function __construct(array $routes = []) {
            
            foreach ($routes as $route) {
    
                $this->addRoute($route);

    
            }
    }

    /**
     * Adds a route to the router.
     *
     * @param Route $route The route to be added.
     */
    public function addRoute(Route $route) {
        $this->routes[] = $route;
    }

    /**
     * Executes the route matching the given url.
     *
     * @param string $url The url to be matched.
     */
    public function execute($url) {
        foreach ($this->routes as $route) {
            if ($route->matches($url)) {
                return $route->execute();
            }
        }
    }
    
}
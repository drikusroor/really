<?php

namespace Ainab\Really;

use Ainab\Really\DI\Container;

class Route
{
    private $path;
    private $action;
    private $matches;
    private $params;
    private $options;
    private $middlewares = [];

    public function __construct($path, $action, private $method = 'GET')
    {
        $this->path = trim($path, '/');
        $this->action = $action;
    }

    public function matches($url, $method = 'GET')
    {
        if ($this->method !== $method) {
            return false;
        }

        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        // Split the URL into path and query parameters
        $urlParts = explode('?', $url);
        $path = $urlParts[0];
        $query = isset($urlParts[1]) ? $urlParts[1] : '';

        if (!preg_match($regex, $path, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;

        // Parse the query parameters and store them in $this->params
        parse_str($query, $this->params);

        return true;
    }

    private function paramMatch($match)
    {
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }

        return '([^/]+)';
    }

    public function execute(Container $container)
    {
        if (is_string($this->action)) {
            $params = explode('@', $this->action);
            $controllerName = $params[0];
            $method = $params[1];

            try {
                $controller = $container->make($controllerName);

                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $this->matches);
                }
            } catch (\Exception $e) {
                return $this->handleNotFound($container, $e->getMessage());
            }
        }

        return call_user_func_array($this->action, $this->matches);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function prependPath($prefix)
    {
        $newPath = trim($prefix, '/') . '/' . $this->path;

        $this->path = trim($newPath, '/');
    }

    public function setMiddlewares($middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    protected function handleNotFound($container, $message = null)
    {
        return $container->make('ErrorController')->notFound($message);
    }
}

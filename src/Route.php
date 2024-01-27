<?php

namespace Ainab\Really;

class Route
{

    private $path;
    private $action;
    private $matches;
    private $params;
    private $options;

    public function __construct($path, $action)
    {
        $this->path = trim($path, '/');
        $this->action = $action;
    }

    public function matches($url)
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {

            return false;
        }

        array_shift($matches);
        $this->matches = $matches;

        return true;
    }

    private function paramMatch($match)
    {
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }

        return '([^/]+)';
    }

    public function execute($container)
    {
        if (is_string($this->action)) {
            $params = explode('@', $this->action);
            $controllerName = $params[0];
            $method = $params[1];

            try {
                $container->make($controllerName);

                $controller = $container->make($controllerName);

                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $this->matches);
                }
            } catch (\Exception $e) {
                return $this->handleNotFound($container);
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

    protected function handleNotFound($container)
    {
        return $container->make('ErrorController')->notFound();
    }
}

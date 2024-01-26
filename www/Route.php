<?php

class Route {
    
        private $path;
        private $action;
        private $matches;
    
        public function __construct($path, $action) {
    
            $this->path = trim($path, '/');
            $this->action = $action;
    
        }
    
        public function matches($url) {
    
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
    
        private function paramMatch($match) {
    
            if (isset($this->params[$match[1]])) {
    
                return '(' . $this->params[$match[1]] . ')';
    
            }
    
            return '([^/]+)';
    
        }
    
        public function execute() {
    
            if (is_string($this->action)) {
    
                $params = explode('@', $this->action);
                $controller = new $params[0]();
                $method = $params[1];
    
                if (method_exists($controller, $method)) {
    
                    return call_user_func_array([$controller, $method], $this->matches);
    
                }
    
            }
    
            return call_user_func_array($this->action, $this->matches);
    
        }
}
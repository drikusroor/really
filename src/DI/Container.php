<?php

namespace Ainab\Really\DI;

class Container {
    protected $bindings = [];

    public function bind($abstract, $concrete = null) {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
    }

    public function make($abstract, $parameters = []) {
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding for {$abstract}");
        }
        return $this->resolve($this->bindings[$abstract], $parameters);
    }

    protected function resolve($concrete, $parameters) {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new \ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }
        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    protected function resolveDependencies($dependencies) {
        $results = [];
        foreach ($dependencies as $dependency) {
            $results[] = $this->make($dependency->getClass()->name);
        }
        return $results;
    }
}

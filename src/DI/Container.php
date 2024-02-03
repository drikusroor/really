<?php

namespace Ainab\Really\DI;

class Container
{
    protected $bindings = [];

    public function bind($abstract, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
    }

    public function make($abstract, $parameters = [])
    {
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding for {$abstract}");
        }
        return $this->resolve($this->bindings[$abstract], $parameters);
    }

    protected function resolve($concrete, $parameters)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new \ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }
        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete();
        }
        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    protected function resolveDependencies($dependencies)
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // Check if the dependency has a class
            if ($dependency->hasType() && !$dependency->getType()->isBuiltin()) {
                // Get the fully qualified class name (FQCN) from the type hint
                $fqcn = $dependency->getType()->getName();
                // Extract the short class name from the FQCN
                $shortClassName = $this->getShortClassName($fqcn);
                // Use the short class name to resolve the dependency
                $results[] = $this->make($shortClassName);
            } else {
                // Handle non-class dependencies or provide a default value
                $results[] = $dependency->isDefaultValueAvailable() ? $dependency->getDefaultValue() : null;
            }
        }
        return $results;
    }

    protected function getShortClassName($className)
    {
        // Use the ReflectionClass to extract the short name
        $reflector = new \ReflectionClass($className);
        return $reflector->getShortName();
    }



    public function has($abstract)
    {
        return isset($this->bindings[$abstract]);
    }
}

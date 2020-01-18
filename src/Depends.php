<?php

namespace metrica\core;

use InvalidArgumentException;

class Depends implements DependsInterface
{
  protected array $components = [];
  protected array $instances = [];

  public function __construct(array $dependencies = [])
  {
    $this->addDependencies($dependencies);
  }

  public function getComponents(): array
  {
    return $this->components;
  }

  public function getInstances(): array
  {
    return $this->instances;
  }

  public function addDependencies(array $dependencies): DependsInterface
  {
    foreach($dependencies as $name => $dependency) {
      if(is_callable($dependency)) {
        $this->addConstructor($name, $dependency);
      } else if(is_object($dependency)) {
        $this->addInstance($name, $dependency);
      } else {
        $this->add($name, $dependency);
      }
    }
    return $this;
  }

  public function has(string $name): bool
  {
    return isset($this->components[$name]) || isset($this->instances[$name]);
  }

  public function add(string $name, string $class): DependsInterface
  {
    return $this->addComponent($name, $class);
  }

  public function addConstructor(string $name, callable $constructor): DependsInterface
  {
    return $this->addComponent($name, $constructor);
  }

  public function addInstance(string $name, object $instance): DependsInterface
  {
    if(isset($this->instances[$name])) {
      throw new Exception('Component ' . $name . ' is already registered');
    }
    $this->instances[$name] = $instance;
    return $instance;
  }

  protected function addComponent(string $name, $value): DependsInterface
  {
    if(isset($this->components[$name])) {
      throw new Exception('Component ' . $name . ' is already registered');
    }
    $this->components[$name] = $value;
    return $this;
  }

  public function get(string $name): object
  {
    if(isset($this->instances[$name])) {
      return $this->instances[$name];
    }
    $instance = $this->create($name);
    $this->instances[$name] = $instance;
    return $instance;
  }

  public function create(string $name, array $extraParams = []): object
  {
    if(!isset($this->components[$name])) {
      throw new Exception('Component ' . $name . ' does not exist');
    }
    $constructor = $this->components[$name];
    if(is_string($constructor) && class_exists($constructor)) {
      $reflectionClass = new \ReflectionClass($constructor);
      if($reflectionMethod = $reflectionClass->getConstructor()) {
        $params = $this->resolveParameterInstances($reflectionMethod->getParameters(), $extraParams);
        return $reflectionClass->newInstanceArgs($params);
      }
      return $reflectionClass->newInstance();
    } else if(is_array($constructor)) {
      $reflectionMethod = new \ReflectionMethod($constructor[0], $constructor[1]);
      $params = $this->resolveParameterInstances($reflectionMethod->getParameters(), $extraParams);
      return $reflectionMethod->invokeArgs(null, $params);
    } else if(is_callable($constructor)) {
      $reflectionMethod = new \ReflectionFunction($constructor);
      $params = $this->resolveParameterInstances($reflectionMethod->getParameters(), $extraParams);
      return $reflectionMethod->invokeArgs($params);
    }
    throw new Exception('Component ' . $name . ' constructor is not valid: ' . var_export($constructor, true));
  }

  public function params($callable, array $extraParams = []): array
  {
    if(is_array($callable)) {
      $reflectionMethod = new \ReflectionMethod($callable[0], $callable[1]);
    } else if(is_callable($callable)) {
      $reflectionMethod = new \ReflectionFunction($callable);
    } else {
      throw new InvalidArgumentException('Unsupported $callable');
    }
    return $this->resolveParameterInstances($reflectionMethod->getParameters(), $extraParams);
  }

  protected function isSameType($param, $type): bool
  {
    return $type === null || $type->getName() === gettype($param) || is_a($param, $type->getName(), true);
  }

  protected function requireParamType($name, $param, $type)
  {
    if(!$this->isSameType($param, $type)) {
      throw new Exception('Incompatible type for parameter ' . $name . ', should be ' . gettype($param));
    }
    return $param;
  }

  protected function resolveParameterInstances(array $params, array $extraParams): array
  {
    return array_map(function($param) use($extraParams) {
      $name = $param->name;
      $type = $param->getType();
      if(isset($extraParams[$name])) {
        return $this->requireParamType($name, $extraParams[$name], $type);
      } else if($this->has($name)) {
        $instance = $this->get($name);
        return $this->requireParamType($name, $instance, $type);
      } else if(!$param->isOptional()) {
        throw new Exception('Missing required component for parameter ' . $name);
      }
      return $param->getDefaultValue();
    }, $params);
  }
}

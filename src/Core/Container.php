<?php
declare(strict_types=1);

namespace Core;

use ReflectionClass;
use Closure;

class Container
{
  private array $registry = [];
  
  public function save(string $key, Closure $value): void 
  {
    $this->registry[$key] = $value;
  }
  
  public function get(string $class): object
  {
    if (array_key_exists($class, $this->registry)) {
      return $this->registry[$class]();
    }
    
    $dependencies = [];
    $reflection = new ReflectionClass($class);
    $constructor = $reflection->getConstructor();

    if (! $constructor) {
      return new $class;
    }

    $parametersAsDependencies = $constructor->getParameters();

    foreach ($parametersAsDependencies as $dependency) {
      $dependencyClassNameFromType = (string) $dependency->getType();
      $dependencies[] = $this->get($dependencyClassNameFromType);
    }

    return new $class(...$dependencies);
  }
}

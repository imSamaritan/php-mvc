<?php
declare(strict_types=1);

namespace Core;

use ReflectionClass;

class Container
{
  public function get(string $class): object
  {
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

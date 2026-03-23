<?php
declare(strict_types=1);

namespace Core;

use ReflectionClass;
use ReflectionNamedType;
use Closure;
use InvalidArgumentException;

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

    if (!$constructor) {
      return new $class();
    }

    $parametersAsDependencies = $constructor->getParameters();

    foreach ($parametersAsDependencies as $dependency) {
      $type = $dependency->getType();

      if ($type === null) {
        throw new InvalidArgumentException("Constructor '{$class}' can not resolve a parameter with a 'NULL' type!");
      }

      if (!$type instanceof ReflectionNamedType) {
        throw new InvalidArgumentException("Constructor '{$class}' can not resolve a parameter with type '{$type}', only ReflectionNamedTypes are allowed.");
      }

      if ($type->isBuiltin()) {
        throw new InvalidArgumentException("Constructor '{$class}' can resolve a built in type '{$type}', consider using a registry for type of constructor!");
      }

      $dependencies[] = $this->get((string) $type);
    }

    return new $class(...$dependencies);
  }
}

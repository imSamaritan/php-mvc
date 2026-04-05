<?php
declare(strict_types=1);

namespace Core;

use ReflectionMethod;
use ReflectionType;
use Core\Exceptions\PageNotFoundException;
use RuntimeException;
use BadMethodCallException;

class Dispatcher
{
  private string $namespace = "App\Controllers";
  public function __construct(
    private Router $router,
    private Container $container,
  ) {}
  public function handle(string $url_path): void
  {
    #Matching incoming route path against the application routes
    $params = $this->router->match($url_path);

    if ($params === false) {
      throw new PageNotFoundException("Resource '{$url_path}', was not found!");
    }

    #Get controller name
    $controller = $this->getControllerName($params);

    #Get action name as a method
    $method = $this->getMethodName($params);

    #Check if a class does not exists, then exit if it true
    if (class_exists($controller) === false) {
      throw new RuntimeException(
        "Constructor '{$controller}', is not defined or does not exists!",
      );
    }

    #Check if a method exist inside the controller constructor
    if (method_exists($controller, $method) === false) {
      throw new BadMethodCallException(
        "Method '{$method}', does not exists inside '{$controller}' constructor!",
      );
    }

    #Instantiate constructor object instance
    $controller_instance = $this->container->get($controller);

    #Method arguments
    $method_args = $this->getMethodArgs($controller, $method, $params);

    #Run a method from the constructor instance
    $controller_instance->$method(...$method_args);
  }

  private function getMethodName(array $params): string
  {
    $method_name = strtolower($params["action"]);
    $method_name = ucwords($method_name, "-");
    $method_name = str_replace("-", "", $method_name);
    $method_name = lcfirst($method_name);

    return $method_name;
  }

  private function getControllerName(array $params): string
  {
    $controller_name = strtolower($params["controller"]);
    $controller_name = ucwords($controller_name, "-");
    $controller_name = str_replace("-", "", $controller_name);

    $namespace = $this->namespace;

    if (isset($params["namespace"])) {
      $namespace .= "\\" . ucwords($params["namespace"]);
    }

    return $namespace . "\\" . $controller_name;
  }

  private function getMethodArgs(
    string $controller_name,
    string $method_name,
    array $params,
  ): array {
    $args = [];
    $reflection = new ReflectionMethod($controller_name, $method_name);
    $parameters = $reflection->getParameters();

    foreach ($parameters as $parameter) {
      $arg_name = $parameter->getName();
      $arg_type = $parameter->getType();

      if (!isset($params[$arg_name])) {
        if ($parameter->getDefaultValue()) {
          $args[$arg_name] = $parameter->getDefaultValue();
          continue;
        }
        throw new RuntimeException("Missing a route parameter '{$arg_name}'!");
      }

      $args[$arg_name] = $this->typeCastAndReturnValue(
        $arg_type,
        $params[$arg_name],
      );
    }

    return $args;
  }

  private function typeCastAndReturnValue(
    ReflectionType $type,
    mixed $value,
  ): mixed {
    $type = (string) $type;
    return match ($type) {
      "int" => (int) $value,
      "float" => (float) $value,
      "object" => (object) $value,
      "array" => (array) $value,
      default => (string) $value,
    };
  }
}

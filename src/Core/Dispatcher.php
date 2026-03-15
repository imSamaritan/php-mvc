<?php
declare(strict_types=1);

namespace Core;

class Dispatcher
{
  private string $namespace = "App\Controllers";
  public function __construct(private Router $router) {}
  public function handle(string $url_path): void
  {
    #Matching incoming route path against the application routes
    $params = $this->router->match($url_path);

    if ($params === false) {
      exit("Resource '{$url_path}', was not found!");
    }

    #Get controller name
    $controller = $this->getControllerName($params);

    #Get action name as a method
    $method = $this->getMethodName($params);

    #Check if a class does not exists, then exit if it true
    if (class_exists($controller) === false) {
      exit("Constructor '{$controller}', is not defined or does not exists!");
    }

    #Check if a method exist inside the controller constructor
    if (method_exists($controller, $method) === false) {
      exit(
        "Method '{$method}', does not exists inside '{$controller}' constructor!"
      );
    }

    #Instantiate constructor object instance
    $controller_instance = new $controller();

    #Run a method from the constructor instance
    $controller_instance->$method();
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

    return $this->namespace . "\\" . $controller_name;
  }
}

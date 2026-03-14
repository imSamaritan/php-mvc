<?php
declare(strict_types=1);

namespace Core;

class Dispatcher
{
  public function __construct(private Router $router) {}
  public function handle(string $url_path): void
  {
    #Matching incoming route path against the application routes
    $matched_route = $this->router->match($url_path);

    if ($matched_route === false) {
      exit("Resource '{$url_path}', was not found!");
    }

    #Get controller name
    $namespace = "App\Controllers\\";
    $controller = $namespace . ucwords($matched_route["controller"]);

    #Get action name as a method
    $method = $matched_route["action"];

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
}

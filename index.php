<?php
#Enable strict types
declare(strict_types=1);

#Get url path
$url_path = parse_url($_SERVER["REQUEST_URI"], 5);

if ($url_path === false) {
  throw new UnexpectedValueException("URL is malformed!!");
}

# Throw errors as exception
set_error_handler(function(int $errno, string $errmsg, string $errfile, int $errline) {
  throw new ErrorException($errmsg, 0, $errno, $errfile, $errline);
});

set_exception_handler(function ($exception) {
  $show_errors = true;

  if ($exception instanceof Core\Exceptions\PageNotFoundException) {
    $view = "404";
    http_response_code(404);
  } else {
    $view = "500";
    http_response_code(500);
  }

  if ($show_errors) {
    # Development
    ini_set("display_errors", 1);
    ini_set("log_errors", 0);
  } else {
    # Production
    ini_set("display_errors", 0);
    ini_set("log_errors", 1);
    require __DIR__ . "/views/{$view}.php";
  }

  throw $exception;
});

#Load modules
require __DIR__ . "/src/autoload.php";

#Instantiate router object
$router = new Core\Router();

#Add routes into a routing table
// Home
$router->add("/", ["controller" => "home", "action" => "index"]);
$router->add("/{controller:home|blogs}", ["action" => "index"]);
// Dynamic
$router->add("/{controller:moderator}/{action:print-role}", [
  "namespace" => "role",
]);
$router->add("/{controller}/{action}/{id:\d+}");
$router->add("/{controller}/{action}");

#Container instance
$container = new Core\Container();

#Add database object into a registry as the constructor is madeup of built in prop's types
$container->save(App\Database::class, function () {
  return new App\Database("localhost", "root", "617808", "blog");
});

# Instantiate dispatcher instance
$dispatcher = new Core\Dispatcher($router, $container);

#handle requests
$dispatcher->handle($url_path);

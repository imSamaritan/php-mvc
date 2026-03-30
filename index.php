<?php
#Enable strict types
declare(strict_types=1);

#Load modules
require __DIR__ . "/src/autoload.php";

# Throw errors as exception
set_error_handler("Core\ExceptionHandler::error");

set_exception_handler("Core\ExceptionHandler::exception");

#Get url path
$url_path = parse_url($_SERVER["REQUEST_URI"], 5);

# Check if the url is malformed
if ($url_path === false) {
  throw new Core\Exceptions\UrlMailformedException("URL is malformed!!");
}

# Router
$router = require __DIR__ . "/config/routes.php";

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

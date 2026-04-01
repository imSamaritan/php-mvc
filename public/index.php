<?php
#Enable strict types
declare(strict_types=1);

#Load modules
require __DIR__ . "/src/autoload.php";

# Throw errors as exception
set_error_handler("Core\ExceptionHandler::error");

set_exception_handler("Core\ExceptionHandler::exception");

# Env Instance
$dotEnv = new Core\DotEnv();

# Read the .env file and populate $_ENV superglobal
$dotEnv->load(__DIR__ . "/.env");

#Get url path
$url_path = parse_url($_SERVER["REQUEST_URI"], 5);

# Check if the url is malformed
if ($url_path === false) {
  throw new Core\Exceptions\UrlMailformedException("URL is malformed!!");
}

# Router
$router = require __DIR__ . "/config/routes.php";

# Container (service)
$container = require __DIR__ . "/config/services.php";

# Instantiate dispatcher instance
$dispatcher = new Core\Dispatcher($router, $container);

#handle requests
$dispatcher->handle($url_path);

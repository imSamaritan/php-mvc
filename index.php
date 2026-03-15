<?php
#Enable strict types
declare(strict_types=1);

#Get url path
$url_path = parse_url($_SERVER["REQUEST_URI"], 5);

#Load modules
require __DIR__ . "/src/autoload.php";

#Instantiate router object
$router = new Core\Router();

#Add routes into a routing table
// Home
$router->add("/", ["controller" => "home", "action" => "index"]);
$router->add("/{author:[\w-]+}/{title:[\w-]+}/{page:\d+}", [
  "controller" => "blogs",
  "action" => "post",
]);
$router->add("/{controller:home|blogs}", [
  "controller" => "home",
  "action" => "index",
]);

// Dynamic
$router->add("/{controller:moderator}/{action:print-role}", ["namespace" => "role"]);
$router->add("/{controller}/{action}");

# Instantiate dispatcher instance
$dispatcher = new Core\Dispatcher($router);
$dispatcher->handle($url_path);

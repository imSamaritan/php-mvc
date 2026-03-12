<?php
#Enable strict types
declare(strict_types=1);

#Get url path
$url_path = parse_url($_SERVER["REQUEST_URI"], 5);

#Load modules
require __DIR__ . "/src/autoload.php";

#Instantiate router object
$router = new Core\Router;

#Add routes into a routing table
$router->add("/", ["controller" => "home", "action" => "index"]);
$router->add("/home", ["controller" => "home", "action" => "index"]);
$router->add("/home/index", ["controller" => "home", "action" => "index"]);

#Matching incoming route path against the application routes
$matched_route = $router->match($url_path);

if ($matched_route === false) {
  exit("Resource '{$url_path}', was not found!");
}

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
$router->add("/home", ["controller" => "home", "action" => "index"]);
$router->add("/home/index", ["controller" => "home", "action" => "index"]);

// Blogs
$router->add("/blogs", ["controller" => "blogs", "action" => "index"]);
$router->add("/blogs/index", ["controller" => "blogs", "action" => "index"]);

#Matching incoming route path against the application routes
$matched_route = $router->match($url_path);

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

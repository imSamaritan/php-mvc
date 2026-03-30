<?php

declare(strict_types=1);

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

return $router;

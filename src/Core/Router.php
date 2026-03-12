<?php
declare(strict_types=1);

namespace Core;

class Router
{
  private array $routes = [];

  public function add(string $path, array $params = []): void
  {
    $this->routes[] = ["path" => $path, "params" => $params];
  }

  public function match(string $url_path): array | false
  {
    $url_path = strtolower($url_path);
    $url_path = rtrim($url_path, "/");

    foreach ($this->routes as $route) {
      if ($route["path"] === $url_path) {
        return $route["params"];
      }
    }

    return false;
  }
}

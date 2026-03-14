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

  public function match(string $url_path): array|false
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

  private function createMatchPattern(string $route_path): string
  {
    $route_path = trim(strtolower($route_path), "/");
    $route_segments = explode(string: $route_path, separator: "/");
    $route_segments = array_map(function (string $segment): string {
      $pattern = "#^\{([a-z][a-z]+)\}$#";
      if (preg_match($pattern, $segment, $matches)) {
        return "(?<" . $matches[1] . ">[a-z]+)";
      }
      return $segment;
    }, $route_segments);

    return "#^" . implode(array: $route_segments, separator: "/") . "$#";
  }
}

<?php

declare(strict_types=1);

namespace Core;

class DotEnv
{
  public function load(string $file_path): void
  {
    $file_lines = file($file_path, FILE_IGNORE_NEW_LINES);
    foreach ($file_lines as $line) {
      if ($line === "" || str_starts_with($line, "#") || !strpos($line, "=")) continue;
      
      $line = explode("=", $line, 2);
      [$key, $value] = $line;
      $_ENV[trim($key)] = trim($value);
    }
  }
}

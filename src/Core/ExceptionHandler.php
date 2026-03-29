<?php 
declare(strict_types=1);

namespace Core;

use Core\Exceptions\PageNotFoundException;
use Core\Exceptions\UrlMailformedException;
use ErrorException;

class ExceptionHandler {
  
  public static function error (
    int $errno,
    string $errmsg,
    string $errfile,
    int $errline,
  ) {
    throw new ErrorException($errmsg, 0, $errno, $errfile, $errline);
  }
  
  public static function exception($exception): void {
    $show_errors = true;
  
    if ($exception instanceof PageNotFoundException) {
      $view = "404";
      http_response_code(404);
    } else {
      $view = "500";
      http_response_code(500);
    }
  
    if ($exception instanceof UrlMailformedException) {
      $view = "malformed-url";
      http_response_code(400);
    }
  
    if ($show_errors) {
      # Development
      ini_set("display_errors", 1);
      ini_set("log_errors", 0);
    } else {
      # Production
      ini_set("display_errors", 0);
      ini_set("log_errors", 1);
      ini_set("error_log", dirname(__DIR__, 2) . "/logs/errors.log");
      require dirname(__DIR__, 2) . "/views/{$view}.php";
    }
  
    throw $exception;
  }
}
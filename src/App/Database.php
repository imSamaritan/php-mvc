<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use Exception;

class Database
{
  private ?PDO $pdo = null;
  private static ?self $instance = null;

  private function __construct() {}
  private function __clone() {}
  public function __wakeup()
  {
    throw new Exception("Cannot unserialize a singleton.");
  }

  public static function instance(): self
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  
}

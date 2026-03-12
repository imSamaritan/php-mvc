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

  public function connect(): PDO
  {
    if ($this->pdo === null) {
      try {
        $dsn = "mysql:host=localhost;dbname=blog;";
        $this->pdo = new PDO($dsn, "root", "617808", [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
      } catch (PDOException $error) {
        $this->pdo = null;
        throw new Exception("Database connection error: {$error->getMessage()}");
      }
    }
    return $this->pdo;
  }
}

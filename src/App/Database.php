<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use Exception;

class Database
{
  private ?PDO $pdo = null;

  public function __construct(
    private string $host = "localhost",
    private string $user = "root",
    private string $password = "617808",
    private string $db_name = "blog",
  ) {}

  public function connect(): PDO
  {
    if ($this->pdo === null) {
      try {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=UTF8";
        $this->pdo = new PDO($dsn, $this->user, $this->password, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
      } catch (PDOException $error) {
        $this->pdo = null;
        throw new Exception(
          "Database connection error: {$error->getMessage()}",
        );
      }
    }
    return $this->pdo;
  }
}

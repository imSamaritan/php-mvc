<?php
declare(strict_types=1);

namespace Core;
use App\Database;

class Model
{
  protected ?string $table = null;
  public function __construct(private Database $db) {}

  private function getTable()
  {
    if ($this->table === null) {
      $table_name_array = explode("\\", $this::class);
      $table = strtolower(array_pop($table_name_array)) . "s";
      $this->table = $table;
    }

    return $this->table;
  }

  public function findAll(): array|false
  {
    $table = $this->getTable();
    $connection = $this->db->connect();
    
    $sql = "SELECT * FROM {$table}";
    $statement = $connection->prepare($sql);

    if ($statement->execute()) {
      return $statement->fetchAll();
    }

    return false;
  }

  public function find(int $id): object|false
  {
    $table = $this->getTable();
    $connection = $this->db->connect();
    
    $sql = "SELECT * FROM {$table} WHERE id = :id";
    $statement = $connection->prepare($sql);

    if ($statement->execute(["id" => $id])) {
      return $statement->fetch();
    }

    return false;
  }
}

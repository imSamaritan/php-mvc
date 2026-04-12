<?php
declare(strict_types=1);

namespace Core;
use App\Database;
use PDO;

class Model
{
  protected ?string $table = null;
  protected array $errors = [];

  public function __construct(protected Database $db) {}

  protected function getTable()
  {
    if ($this->table === null) {
      $table_name_array = explode("\\", $this::class);
      $table = strtolower(array_pop($table_name_array)) . "s";
      $this->table = $table;
    }

    return $this->table;
  }

  protected function addError(string $field, string $message): void
  {
    $this->errors[$field] = $message;
  }

  public function getError(): array
  {
    return $this->errors;
  }
  
  private function getId(): int 
  {
    $connection = $this->db->connect();
    return (int) $connection->lastInsertId();
  }
  
  protected function validate(array $data): void {}

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

  public function create(array $data): int|bool
  {
    $this->validate($data);
    if (! empty($this->errors)) return false;
  

    $table = $this->getTable();
    $connection = $this->db->connect();

    $columns = implode(",", array_keys($data));
    $placeholders = implode(",", array_map(fn() => "?", array_keys($data)));

    $sql = "INSERT INTO {$table}({$columns}) VALUES({$placeholders})";
    $stmt = $connection->prepare($sql);

    $i = 1;
    foreach ($data as $value) {
      $type = gettype($value);
      $pdoType = match ($type) {
        "int" => PDO::PARAM_INT,
        "bool" => PDO::PARAM_BOOL,
        "null" => PDO::PARAM_NULL,
        default => PDO::PARAM_STR,
      };
      $stmt->bindValue($i++, $value, $pdoType);
    }

    if ($stmt->execute()) {
      return $this->getId();
    } else {
      return false;
    }
  }
  
  public function update(int $id, array $data) 
  {
    $this->validate($data);
    if (! empty($this->errors)) return false;
    
    $connection = $this->db->connect();
    $table = $this->getTable();
    
    $fields = array_keys($data);
    $fields = array_map(fn($field) => "{$field} = ?", $fields);
    $fields = implode(",", $fields);
    
    $sql = "UPDATE {$table} SET {$fields} WHERE id = ?";
    $stmt = $connection->prepare($sql);
      
    $i = 1;
    foreach ($data as $value) {
      $type = gettype($value);
      $pdoType = match ($type) {
        "int" => PDO::PARAM_INT,
        "bool" => PDO::PARAM_BOOL,
        "null" => PDO::PARAM_NULL,
        default => PDO::PARAM_STR,
      };
      $stmt->bindValue($i++, $value, $pdoType);
    }
    
    $stmt->bindValue($i, $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
  }
  
  protected function delete(int $id): bool 
  {
    $connection = $this->db->connect();
    $table = $this->getTable();
    
    $sql = "DELETE FROM {$table} WHERE id = ?";
    $stmt = $connection->prepare($sql);
    
    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    
    return $stmt->execute();
  }
}

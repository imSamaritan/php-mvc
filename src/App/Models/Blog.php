<?php
declare(strict_types=1);

namespace App\Models;
use App\Database;

class Blog
{
  public function __construct(private Database $db) {}

  public function findAll(): array|false
  {
    $connection = $this->db->connect();
    $sql = "SELECT * FROM blogs";
    $statement = $connection->prepare($sql);

    if ($statement->execute()) {
      return $statement->fetchAll();
    }

    return false;
  }

  public function find(int $id): object|false
  {
    $connection = $this->db->connect();
    $sql = "SELECT * FROM blogs WHERE id = :id";
    $statement = $connection->prepare($sql);

    if ($statement->execute(["id" => $id])) {
      return $statement->fetch();
    }

    return false;
  }
}

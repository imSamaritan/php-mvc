<?php
declare(strict_types=1);

namespace App\Models;
use Core\Model;

class Blog extends Model
{
  protected function validate(array $data): void
  {
    if (empty($data["author"])) {
      $this->addError("author", "Post author field is required!");
    }

    if (empty($data["title"])) {
      $this->addError("title", "Post title is required!");
    }

    if (empty($data["body"])) {
      $this->addError("body", "Post body is required!");
    }
  }
  
  public function recordsCount(): int
  {
    $connection = $this->db->connect();
    
    $sql = "SELECT COUNT(*) AS records_count FROM {$this->getTable()}";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    
    return (int) $stmt->fetch()->records_count;
  }
  
  public function deletePost(int $id): bool 
  {
    return $this->delete($id);
  }
}

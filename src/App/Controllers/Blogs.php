<?php
declare(strict_types=1);

namespace App\Controllers;

class Blogs
{
  public function index(): void
  {
    echo "Blogs Controller";
  }

  public function post(
    string $author = "",
    string $title = "",
    int $page = 3,
  ): void {
    echo "This is working...";
  }
}

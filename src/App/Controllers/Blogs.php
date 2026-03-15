<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Models\Blog;

class Blogs
{
  private Blog $blog;
  public function __construct()
  {
    $this->blog = new Blog();
  }

  public function index(): void
  {
    echo "Blogs";
    $blogs = $this->blog->findAll();
    echo "<pre>";
    print_r($blogs);
  }

  public function show(int $id = 1): void
  {
    echo "Blog #{$id}";
    $blog = $this->blog->find($id);
    echo "<pre>";
    print_r($blog);
  }
}

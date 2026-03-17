<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Blog;
use Core\Viewer;

class Blogs
{
  private Blog $blog;
  private Viewer $viewer;
  
  public function __construct()
  {
    $this->blog = new Blog();
    $this->viewer = new Viewer();
  }

  public function index(): void
  {
    $blogs = $this->blog->findAll();
    
    echo $this->viewer->render("shared/header", ["title" => "Blogs"]);    
    echo $this->viewer->render("blogs/index", ["blogs" => $blogs]);
  }

  public function show(int $id = 1): void
  {
    $blog = $this->blog->find($id);
    
    echo $this->viewer->render("shared/header", ["title" => "Blog #{$blog->id}"]);
    echo $this->viewer->render("blogs/show", ["blog" => $blog]);
  }
}

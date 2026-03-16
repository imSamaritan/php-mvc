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
    #Get data
    $blogs = $this->blog->findAll();

    #Render data
    echo $this->viewer->render("blogs/index", ["blogs" => $blogs]);
  }

  public function show(int $id = 1): void
  {
    #Get data
    $blog = $this->blog->find($id);

    #Render single post
    echo $this->viewer->render("blogs/show", ["blog" => $blog]);
  }
}

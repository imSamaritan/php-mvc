<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Blog;
use Core\Exceptions\PageNotFoundException;
use Core\Viewer;

class Blogs
{
  public function __construct(private Blog $blog, private Viewer $viewer) {}

  public function index(): void
  {
    $blogs = $this->blog->findAll();
    $recordsCount = $this->blog->recordsCount();
    
    echo $this->viewer->render("shared/header", ["title" => "Blogs"]);
    echo $this->viewer->render("blogs/index", ["blogs" => $blogs, "recordsCount" => $recordsCount]);
    echo $this->viewer->render("shared/footer");
  }

  public function show(int $id): void
  {
    $blog = $this->blog->find($id);

    if ($blog === false) {
      throw new PageNotFoundException(
        "Resource with an id '{$id}', was not found!",
      );
    }

    echo $this->viewer->render("shared/header", [
      "title" => "Blog #{$blog->id}",
    ]);
    echo $this->viewer->render("blogs/show", ["blog" => $blog]);
    echo $this->viewer->render("shared/footer");
  }

  public function create()
  {
    if (isset($_POST["submit"])) {
      unset($_POST["submit"]);
      $postID = $this->blog->create($_POST);
      
      if ($postID) {
        header("Location: /blogs/show/{$postID}");
        exit();
      }
      else {
        $error = $this->blog->getError();
        echo $this->viewer->render("shared/header", ["title" => "Create post"]);
        echo $this->viewer->render("blogs/create", ["error" => $error]);
        echo $this->viewer->render("shared/footer");
        exit();
      }
    } else {
      echo $this->viewer->render("shared/header", ["title" => "Create post"]);
      echo $this->viewer->render("blogs/create");
      echo $this->viewer->render("shared/footer");
    }
  }
}

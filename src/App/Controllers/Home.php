<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Viewer;

class Home
{
  private Viewer $viewer;

  public function __construct()
  {
    $this->viewer = new Viewer();
  }

  public function index(): void
  {
    echo $this->viewer->render("home/index");
  }
}

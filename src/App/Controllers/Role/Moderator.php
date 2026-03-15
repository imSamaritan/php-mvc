<?php
declare(strict_types=1);

namespace App\Controllers\Role;

class Moderator
{
  public function printRole()
  {
    $arr = explode("\\", self::class);
    echo "Role : " . array_pop($arr);
  }
}

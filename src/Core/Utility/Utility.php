<?php 
declare(strict_types=1);

namespace Core\Utility;
use UnexpectedValueException;

class Utility {
  public static function getShowError(string $show_errors): mixed 
  {
    if ($show_errors === "false" || $show_errors === "0") return 0;
    if ($show_errors === "true"  || $show_errors === "1") return 1;
    
    throw new UnexpectedValueException("[SHOW_ERRORS] in the .env file, must be 0, false OR 1, true");
  }
}
<?php
declare(strict_types=1);

spl_autoload_register("load");

function load(string $module_full_namespace): void
{
  $module = str_replace("\\", "/", $module_full_namespace);
  require_once dirname(__DIR__) . "/src/{$module}.php";
}

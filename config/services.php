<?php
declare(strict_types=1);

#Container instance
$container = new Core\Container();

#Add database object into a registry as the constructor is madeup of built in prop's types
$container->save(App\Database::class, function () {
  return new App\Database("localhost", "root", "617808", "blog");
});

return $container;

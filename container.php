<?php

/*
This file returns the global DI container for the application. The DI container
must be compatible with the symfony DI container interface. If you want load an 
different configuration depending on the environment you can change the 
"config.file" parameter.
*/

$container = new \PSX\Framework\App\Dependency\Container();
$container->setParameter('config.file', __DIR__ . '/configuration.php');

return $container;

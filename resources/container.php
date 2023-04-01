<?php

use PSX\Framework\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->instanceof(ControllerInterface::class)
        ->tag('psx.controller');

    $services->load('App\\Controller\\', __DIR__ . '/../src/Controller')
        ->public();

    $services->load('App\\Service\\', __DIR__ . '/../src/Service')
        ->public();

};

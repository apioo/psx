<?php

require __DIR__ . '/../vendor/autoload.php';

PSX\Framework\Test\Environment::setup(__DIR__ . '/..', function ($fromSchema) {
    return PSX\Project\Tests\TestSchema::getSchema();
});

$container = \PSX\Framework\Test\Environment::getContainer();

$container->set('population_service', new PSX\Project\Tests\Service\Population(
    $container->get('table_manager')->getTable('PSX\Project\Tests\Table\Population')
));

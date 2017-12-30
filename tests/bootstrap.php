<?php

require __DIR__ . '/../vendor/autoload.php';

PSX\Framework\Test\Environment::setup(__DIR__ . '/..', function ($fromSchema) {
    return \PSX\Framework\App\TestSchema::getSchema();
});

<?php

return \PSX\Framework\Dependency\ContainerBuilder::build(
    __DIR__,
    getenv('PSX_ENV') !== 'prod',
    __DIR__ . '/vendor/psx/framework/resources/container.php',
    __DIR__ . '/resources/container.php',
);

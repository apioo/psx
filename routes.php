<?php

return [

    # API
    [['ANY'], '/population/popo', \PSX\Framework\App\Api\Population\CollectionPopo::class],
    [['ANY'], '/population/popo/:id', \PSX\Framework\App\Api\Population\EntityPopo::class],
    [['ANY'], '/population/typeschema', \PSX\Framework\App\Api\Population\CollectionTypeSchema::class],
    [['ANY'], '/population/typeschema/:id', \PSX\Framework\App\Api\Population\EntityTypeSchema::class],
    [['ANY'], '/population/openapi', \PSX\Framework\App\Api\Population\CollectionOpenAPI::class],
    [['ANY'], '/population/openapi/:id', \PSX\Framework\App\Api\Population\EntityOpenAPI::class],
    [['ANY'], '/population', \PSX\Framework\App\Api\Population\Collection::class],
    [['ANY'], '/population/:id', \PSX\Framework\App\Api\Population\Entity::class],

    # tool controller
    [['GET'], '/tool', \PSX\Framework\Controller\Tool\DefaultController::class],
    [['GET'], '/tool/discovery', \PSX\Framework\Controller\Tool\DiscoveryController::class],
    [['GET'], '/tool/doc', \PSX\Framework\Controller\Tool\Documentation\IndexController::class],
    [['GET'], '/tool/doc/:version/*path', \PSX\Framework\Controller\Tool\Documentation\DetailController::class],
    [['GET'], '/tool/routing', \PSX\Framework\Controller\Tool\RoutingController::class],

    [['GET'], '/generator/:type/:version/*path', \PSX\Framework\Controller\Generator\GeneratorController::class],

];

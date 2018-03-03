<?php

return [

    # API
    [['ANY'], '/population/popo', \PSX\Framework\App\Api\Population\CollectionPopo::class],
    [['ANY'], '/population/popo/:id', \PSX\Framework\App\Api\Population\EntityPopo::class],
    [['ANY'], '/population/jsonschema', \PSX\Framework\App\Api\Population\CollectionJsonSchema::class],
    [['ANY'], '/population/jsonschema/:id', \PSX\Framework\App\Api\Population\EntityJsonSchema::class],
    [['ANY'], '/population/raml', \PSX\Framework\App\Api\Population\CollectionRaml::class],
    [['ANY'], '/population/raml/:id', \PSX\Framework\App\Api\Population\EntityRaml::class],
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

    [['GET'], '/generator/raml/:version/*path', \PSX\Framework\Controller\Generator\RamlController::class],
    [['GET'], '/generator/swagger/:version/*path', \PSX\Framework\Controller\Generator\SwaggerController::class],
    [['GET'], '/generator/openapi/:version/*path', \PSX\Framework\Controller\Generator\OpenAPIController::class],

];

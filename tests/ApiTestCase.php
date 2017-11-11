<?php

namespace PSX\Project\Tests;

use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Project\Tests\Api\Population;
use PSX\Framework\Controller\Generator;
use PSX\Framework\Controller\Proxy;
use PSX\Framework\Controller\Tool;

class ApiTestCase extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/api_fixture.xml');
    }

    protected function getPaths()
    {
        return [
            [['ANY'], '/population/popo', Population\CollectionPopo::class],
            [['ANY'], '/population/popo/:id', Population\EntityPopo::class],
            [['ANY'], '/population/jsonschema', Population\CollectionJsonSchema::class],
            [['ANY'], '/population/jsonschema/:id', Population\EntityJsonSchema::class],
            [['ANY'], '/population/raml', Api\Population\CollectionRaml::class],
            [['ANY'], '/population/raml/:id', Population\EntityRaml::class],
            [['ANY'], '/population/openapi', Population\CollectionOpenAPI::class],
            [['ANY'], '/population/openapi/:id', Population\EntityOpenAPI::class],
            [['ANY'], '/population', Population\Collection::class],
            [['ANY'], '/population/:id', Population\Entity::class],

            [['GET'], '/tool', Tool\DefaultController::class],
            [['GET'], '/tool/discovery', Tool\DiscoveryController::class],
            [['GET'], '/tool/doc', Tool\DocumentationController::class . '::doIndex'],
            [['GET'], '/tool/doc/:version/*path', Tool\DocumentationController::class . '::doDetail'],
            [['GET'], '/tool/routing', Tool\RoutingController::class],

            [['GET'], '/generator/raml/:version/*path', Generator\RamlController::class],
            [['GET'], '/generator/swagger/:version/*path', Generator\SwaggerController::class],
            [['GET'], '/generator/openapi/:version/*path', Generator\OpenAPIController::class],

            [['ANY'], '/proxy/soap', Proxy\SoapController::class],
        ];
    }
}

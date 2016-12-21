<?php

namespace PSX\Project\Tests;

use PSX\Framework\Test\ControllerDbTestCase;

class ApiTestCase extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/api_fixture.xml');
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/popo', 'PSX\Project\Tests\Api\Population\CollectionPopo'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/popo/:id', 'PSX\Project\Tests\Api\Population\EntityPopo'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/jsonschema', 'PSX\Project\Tests\Api\Population\CollectionJsonSchema'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/jsonschema/:id', 'PSX\Project\Tests\Api\Population\EntityJsonSchema'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/raml', 'PSX\Project\Tests\Api\Population\CollectionRaml'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/raml/:id', 'PSX\Project\Tests\Api\Population\EntityRaml'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population', 'PSX\Project\Tests\Api\Population\Collection'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/:id', 'PSX\Project\Tests\Api\Population\Entity'],

            [['GET'], '/tool', 'PSX\Framework\Controller\Tool\DefaultController'],
            [['GET'], '/tool/discovery', 'PSX\Framework\Controller\Tool\DiscoveryController'],
            [['GET'], '/tool/doc', 'PSX\Framework\Controller\Tool\DocumentationController::doIndex'],
            [['GET'], '/tool/doc/:version/*path', 'PSX\Framework\Controller\Tool\DocumentationController::doDetail'],
            [['GET'], '/tool/routing', 'PSX\Framework\Controller\Tool\RoutingController'],

            [['GET'], '/generator/raml/:version/*path', 'PSX\Framework\Controller\Generator\RamlController'],
            [['GET'], '/generator/swagger/:version/*path', 'PSX\Framework\Controller\Generator\SwaggerController'],

            [['GET', 'POST', 'PUT', 'DELETE'], '/proxy/soap', 'PSX\Framework\Controller\Proxy\SoapController'],
        );
    }
}

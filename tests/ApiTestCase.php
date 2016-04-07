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
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/annotation', 'PSX\Project\Tests\Api\Population\CollectionAnnotation'],
            [['GET', 'POST', 'PUT', 'DELETE'], '/population/annotation/:id', 'PSX\Project\Tests\Api\Population\EntityAnnotation'],
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
            [['GET'], '/generator/swagger', 'PSX\Framework\Controller\Generator\SwaggerController::doIndex'],
            [['GET'], '/generator/swagger/:version/*path', 'PSX\Framework\Controller\Generator\SwaggerController::doDetail'],
            [['GET'], '/generator/wsdl/:version/*path', 'PSX\Framework\Controller\Generator\WsdlController'],

            [['GET', 'POST', 'PUT', 'DELETE'], '/proxy/soap', 'PSX\Framework\Controller\Proxy\SoapController'],
        );
    }
}

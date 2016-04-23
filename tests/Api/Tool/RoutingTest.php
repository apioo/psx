<?php

namespace PSX\Project\Tests\Api\Tool;

use PSX\Project\Tests\ApiTestCase;

class RoutingTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/tool/routing', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "routings": [
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/popo",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionPopo"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/popo\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityPopo"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/jsonschema",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionJsonSchema"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/jsonschema\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityJsonSchema"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/raml",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionRaml"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/raml\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityRaml"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population",
            "source": "PSX\\Project\\Tests\\Api\\Population\\Collection"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/population\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\Entity"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool",
            "source": "PSX\\Framework\\Controller\\Tool\\DefaultController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/discovery",
            "source": "PSX\\Framework\\Controller\\Tool\\DiscoveryController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/doc",
            "source": "PSX\\Framework\\Controller\\Tool\\DocumentationController::doIndex"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/doc\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Tool\\DocumentationController::doDetail"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/tool\/routing",
            "source": "PSX\\Framework\\Controller\\Tool\\RoutingController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/raml\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\RamlController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/swagger",
            "source": "PSX\\Framework\\Controller\\Generator\\SwaggerController::doIndex"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/swagger\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\SwaggerController::doDetail"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/wsdl\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\WsdlController"
        },
        {
            "methods": [
                "GET",
                "POST",
                "PUT",
                "DELETE"
            ],
            "path": "\/proxy\/soap",
            "source": "PSX\\Framework\\Controller\\Proxy\\SoapController"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}

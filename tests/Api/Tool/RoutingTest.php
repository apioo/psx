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
                "ANY"
            ],
            "path": "\/population\/popo",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionPopo"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/popo\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityPopo"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/jsonschema",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionJsonSchema"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/jsonschema\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityJsonSchema"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/raml",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionRaml"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/raml\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityRaml"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/openapi",
            "source": "PSX\\Project\\Tests\\Api\\Population\\CollectionOpenAPI"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population\/openapi\/:id",
            "source": "PSX\\Project\\Tests\\Api\\Population\\EntityOpenAPI"
        },
        {
            "methods": [
                "ANY"
            ],
            "path": "\/population",
            "source": "PSX\\Project\\Tests\\Api\\Population\\Collection"
        },
        {
            "methods": [
                "ANY"
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
            "path": "\/generator\/swagger\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\SwaggerController"
        },
        {
            "methods": [
                "GET"
            ],
            "path": "\/generator\/openapi\/:version\/*path",
            "source": "PSX\\Framework\\Controller\\Generator\\OpenAPIController"
        },
        {
            "methods": [
                "ANY"
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

<?php

namespace PSX\Project\Tests\Api\Tool;

use PSX\Project\Tests\ApiTestCase;

class DiscoveryTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/tool/discovery', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "links": [
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        },
        {
            "rel": "routing",
            "href": "http:\/\/127.0.0.1\/tool\/routing"
        },
        {
            "rel": "documentation",
            "href": "http:\/\/127.0.0.1\/tool\/doc"
        },
        {
            "rel": "openapi",
            "href": "http:\/\/127.0.0.1\/generator\/openapi\/{version}\/{path}"
        },
        {
            "rel": "swagger",
            "href": "http:\/\/127.0.0.1\/generator\/swagger\/{version}\/{path}"
        },
        {
            "rel": "raml",
            "href": "http:\/\/127.0.0.1\/generator\/raml\/{version}\/{path}"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}

<?php

namespace PSX\Project\Tests\Api\Generator;

use PSX\Project\Tests\ApiTestCase;

class SwaggerTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/swagger/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/swagger.json');

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetCollection()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/swagger/*/*', 'GET');

        $body   = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/swagger_collection.json');

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}

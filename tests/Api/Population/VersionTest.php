<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Json\Parser;
use PSX\Project\Tests\ApiTestCase;

class VersionTest extends ApiTestCase
{
    public function testGetDefaultVersion()
    {
        $response = $this->sendRequest('http://127.0.0.1/population/2', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "id": 2,
    "place": 2,
    "region": "United States",
    "population": 307212123,
    "users": 227719000,
    "worldUsers": 13.1,
    "datetime": "2009-11-29T15:22:40Z"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetExplicitVersion()
    {
        $response = $this->sendRequest('http://127.0.0.1/population/2', 'GET', ['Accept' => 'application/vnd.psx.v2+json']);

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "id": 2,
    "place": 2,
    "region": "United States",
    "population": 307212123,
    "users": 227719000,
    "worldUsers": 13.1,
    "datetime": "2009-11-29T15:22:40Z"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testGetInvalidVersion()
    {
        $response = $this->sendRequest('http://127.0.0.1/population/2', 'GET', ['Accept' => 'application/vnd.psx.v8+json']);

        $body = (string) $response->getBody();
        $body = Parser::decode($body);

        $this->assertEquals(406, $response->getStatusCode(), $body);
        $this->assertEquals(false, $body->success);
        $this->assertEquals('PSX\\Http\\Exception\\NotAcceptableException', $body->title);
        $this->assertEquals('Version is not available', substr($body->message, 0, 24));
    }
}

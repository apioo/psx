<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Test\Environment;
use PSX\Project\Tests\ApiTestCase;

class EntityTest extends ApiTestCase
{
    /**
     * @dataProvider routeDataProvider
     */
    public function testGet($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . str_replace(':id', 1, $path), 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "id": 1,
    "place": 1,
    "region": "China",
    "population": 1338612968,
    "users": 360000000,
    "worldUsers": 20.8,
    "datetime": "2009-11-29T15:21:49Z"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testGetNotFound($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . str_replace(':id', 16, $path), 'GET');

        $body = (string) $response->getBody();

        $this->assertEquals(404, $response->getStatusCode(), $body);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPost($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . str_replace(':id', 1, $path), 'POST');

        $body = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $body);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPut($path)
    {
        $payload = json_encode([
            'id'         => 1,
            'place'      => 11,
            'region'     => 'Foo',
            'population' => 1024,
            'users'      => 512,
            'worldUsers' => 0.6,
        ]);

        $response = $this->sendRequest('http://127.0.0.1/' . str_replace(':id', 1, $path), 'PUT', ['Content-Type' => 'application/json'], $payload);

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Update successful"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'place', 'region', 'population', 'users', 'worldUsers')
            ->from('population')
            ->where('id = :id')
            ->getSQL();

        $result = Environment::getService('connection')->fetchAssoc($sql, ['id' => 1]);
        $expect = [
            'id' => 1,
            'place' => 11,
            'region' => 'Foo',
            'population' => 1024,
            'users' => 512,
            'worldUsers' => 0.6
        ];

        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testDelete($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . str_replace(':id', 1, $path), 'DELETE');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Delete successful"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'place', 'region', 'population', 'users', 'worldUsers')
            ->from('population')
            ->where('id = :id')
            ->getSQL();

        $result = Environment::getService('connection')->fetchAssoc($sql, ['id' => 1]);

        $this->assertEmpty($result);
    }

    public function routeDataProvider()
    {
        return [
            ['population/popo/:id'],
            ['population/jsonschema/:id'],
            ['population/raml/:id'],
        ];
    }
}

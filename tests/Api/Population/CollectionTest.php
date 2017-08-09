<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Test\Environment;
use PSX\Project\Tests\ApiTestCase;

class CollectionTest extends ApiTestCase
{
    /**
     * @dataProvider routeDataProvider
     */
    public function testGetAll($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . $path, 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "totalResults": 10,
    "entry": [
        {
            "id": 10,
            "place": 10,
            "region": "Korea South",
            "population": 48508972,
            "users": 37475800,
            "worldUsers": 2.2,
            "datetime": "2009-11-29T15:28:06Z"
        },
        {
            "id": 9,
            "place": 9,
            "region": "France",
            "population": 62150775,
            "users": 43100134,
            "worldUsers": 2.5,
            "datetime": "2009-11-29T15:27:37Z"
        },
        {
            "id": 8,
            "place": 8,
            "region": "Russia",
            "population": 140041247,
            "users": 45250000,
            "worldUsers": 2.6,
            "datetime": "2009-11-29T15:27:07Z"
        },
        {
            "id": 7,
            "place": 7,
            "region": "United Kingdom",
            "population": 61113205,
            "users": 46683900,
            "worldUsers": 2.7,
            "datetime": "2009-11-29T15:26:27Z"
        },
        {
            "id": 6,
            "place": 6,
            "region": "Germany",
            "population": 82329758,
            "users": 54229325,
            "worldUsers": 3.1,
            "datetime": "2009-11-29T15:25:58Z"
        },
        {
            "id": 5,
            "place": 5,
            "region": "Brazil",
            "population": 198739269,
            "users": 67510400,
            "worldUsers": 3.9,
            "datetime": "2009-11-29T15:25:20Z"
        },
        {
            "id": 4,
            "place": 4,
            "region": "India",
            "population": 1156897766,
            "users": 81000000,
            "worldUsers": 4.7,
            "datetime": "2009-11-29T15:24:47Z"
        },
        {
            "id": 3,
            "place": 3,
            "region": "Japan",
            "population": 127078679,
            "users": 95979000,
            "worldUsers": 5.5,
            "datetime": "2009-11-29T15:23:18Z"
        },
        {
            "id": 2,
            "place": 2,
            "region": "United States",
            "population": 307212123,
            "users": 227719000,
            "worldUsers": 13.1,
            "datetime": "2009-11-29T15:22:40Z"
        },
        {
            "id": 1,
            "place": 1,
            "region": "China",
            "population": 1338612968,
            "users": 360000000,
            "worldUsers": 20.8,
            "datetime": "2009-11-29T15:21:49Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testGetLimited($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . $path . '?startIndex=4&count=4', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "totalResults": 10,
    "startIndex": 4,
    "itemsPerPage": 4,
    "entry": [
        {
            "id": 6,
            "place": 6,
            "region": "Germany",
            "population": 82329758,
            "users": 54229325,
            "worldUsers": 3.1,
            "datetime": "2009-11-29T15:25:58Z"
        },
        {
            "id": 5,
            "place": 5,
            "region": "Brazil",
            "population": 198739269,
            "users": 67510400,
            "worldUsers": 3.9,
            "datetime": "2009-11-29T15:25:20Z"
        },
        {
            "id": 4,
            "place": 4,
            "region": "India",
            "population": 1156897766,
            "users": 81000000,
            "worldUsers": 4.7,
            "datetime": "2009-11-29T15:24:47Z"
        },
        {
            "id": 3,
            "place": 3,
            "region": "Japan",
            "population": 127078679,
            "users": 95979000,
            "worldUsers": 5.5,
            "datetime": "2009-11-29T15:23:18Z"
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPost($path)
    {
        $payload = json_encode([
            'id'         => 11,
            'place'      => 11,
            'region'     => 'Foo',
            'population' => 1024,
            'users'      => 512,
            'worldUsers' => 0.6,
        ]);

        $response = $this->sendRequest('http://127.0.0.1/' . $path, 'POST', ['Content-Type' => 'application/json'], $payload);

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Create population successful"
}
JSON;

        $this->assertEquals(201, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'place', 'region', 'population', 'users', 'worldUsers')
            ->from('population')
            ->orderBy('id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(2)
            ->getSQL();

        $result = Environment::getService('connection')->fetchAll($sql);
        $expect = [
            ['id' => 11, 'place' => 11, 'region' => 'Foo', 'population' => 1024, 'users' => 512, 'worldUsers' => 0.6],
            ['id' => 10, 'place' => 10, 'region' => 'Korea South', 'population' => 48508972, 'users' => 37475800, 'worldUsers' => 2.2],
        ];

        $this->assertEquals($expect, $result);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testPut($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . $path, 'PUT');

        $body = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $body);
    }

    /**
     * @dataProvider routeDataProvider
     */
    public function testDelete($path)
    {
        $response = $this->sendRequest('http://127.0.0.1/' . $path, 'DELETE');

        $body = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $body);
    }

    public function routeDataProvider()
    {
        return [
            ['population/popo'],
            ['population/jsonschema'],
            ['population/raml'],
            ['population/openapi'],
        ];
    }
}

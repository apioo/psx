<?php

namespace App\Tests\Controller;

use App\Model;
use PSX\Framework\Test\ControllerDbTestCase;

class PopulationTest extends ControllerDbTestCase
{
    public function getDataSet(): array
    {
        return $this->createFromFile(__DIR__ . '/../fixture.php');
    }

    public function testGetAll(): void
    {
        $response = $this->sendRequest('/population', 'GET');

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resources/collection.json');

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGet(): void
    {
        $response = $this->sendRequest('/population/1', 'GET');

        $actual = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resources/entity.json');

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGetNotFound(): void
    {
        $response = $this->sendRequest('/population/16', 'GET');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testCreate(): void
    {
        $payload = new Model\Population();
        $payload->setPlace(16);
        $payload->setRegion('Binary');
        $payload->setPopulation(1024);
        $payload->setUsers(512);
        $payload->setWorldUsers(0.5);

        $response = $this->sendRequest('/population', 'POST', [], \json_encode($payload));

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Population record successfully created",
    "id": 11
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testUpdate(): void
    {
        $payload = new Model\Population();
        $payload->setPopulation(1024);

        $response = $this->sendRequest('/population/1', 'PUT', [], \json_encode($payload));

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Population record successfully updated",
    "id": 1
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testUpdateNotFound(): void
    {
        $payload = new Model\Population();
        $payload->setPopulation(1024);

        $response = $this->sendRequest('/population/16', 'PUT', [], \json_encode($payload));

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDelete(): void
    {
        $response = $this->sendRequest('/population/1', 'DELETE');

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "success": true,
    "message": "Population record successfully deleted",
    "id": 1
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testDeleteNotFound(): void
    {
        $response = $this->sendRequest('/population/16', 'DELETE');

        $this->assertEquals(404, $response->getStatusCode());
    }
}

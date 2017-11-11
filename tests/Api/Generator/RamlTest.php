<?php

namespace PSX\Project\Tests\Api\Generator;

use PSX\Project\Tests\ApiTestCase;
use Symfony\Component\Yaml\Yaml;

class RamlTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/raml/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/raml.yaml');

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(Yaml::parse($expect), Yaml::parse($body), $body);
    }

    public function testGetCollection()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/raml/*/*', 'GET');

        $body   = (string) $response->getBody();
        $expect = file_get_contents(__DIR__ . '/resource/raml_collection.yaml');

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertEquals(Yaml::parse($expect), Yaml::parse($body), $body);
    }
}

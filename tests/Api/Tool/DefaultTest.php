<?php

namespace PSX\Project\Tests\Api\Tool;

use PSX\Project\Tests\ApiTestCase;

class DefaultTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/tool', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<JSON
{
    "message": "This is the default controller of PSX",
    "url": "http:\/\/phpsx.org"
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }
}

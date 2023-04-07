<?php

namespace App\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;

class WelcomeTest extends ControllerTestCase
{
    public function testShow(): void
    {
        $response = $this->sendRequest('/', 'GET');

        $actual = (string) $response->getBody();
        $expect = <<<JSON
{
    "message": "Welcome, your PSX installation is working!",
    "url": "https:\/\/phpsx.org",
    "version": "7.1.0.0"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
}

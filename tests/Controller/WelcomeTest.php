<?php

namespace App\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;

class WelcomeTest extends ControllerTestCase
{
    public function testShow(): void
    {
        $response = $this->sendRequest('/', 'GET');

        $actual = (string) $response->getBody();
        $data = \json_decode($actual);

        $this->assertEquals(200, $response->getStatusCode(), $actual);
        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('Welcome, your PSX installation is working!', $data->message ?? null);
        $this->assertEquals('https://phpsx.org', $data->url ?? null);
    }
}

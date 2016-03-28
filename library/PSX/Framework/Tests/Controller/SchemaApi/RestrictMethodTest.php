<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Tests\Controller\SchemaApi;

use PSX\Data\Record;
use PSX\Data\Writer;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Json;

/**
 * RestrictMethodTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RestrictMethodTest extends ControllerTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'GET', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testPost()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('GET, DELETE', $response->getHeader('Allow'));
    }

    public function testPut()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'PUT', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('GET, DELETE', $response->getHeader('Allow'));
    }

    public function testDelete()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'DELETE', ['Content-Type' => 'application/json']);
        $body     = (string) $response->getBody();

        $this->assertEquals(204, $response->getStatusCode());
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\SchemaApi\RestrictMethodController'],
        );
    }
}

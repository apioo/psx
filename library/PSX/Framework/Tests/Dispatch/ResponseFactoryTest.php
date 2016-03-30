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

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\ResponseFactory;

/**
 * ResponseFactoryTest
 *
 * @see     http://www.ietf.org/rfc/rfc3875
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $server;

    protected function setUp()
    {
        parent::setUp();

        // the test modifies the global server variable so store and reset the
        // values after the test
        $this->server = $_SERVER;
    }

    protected function tearDown()
    {
        parent::tearDown();

        $_SERVER = $this->server;
    }

    public function testCreateResponse()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';

        $factory  = new ResponseFactory();
        $response = $factory->createResponse();

        $this->assertEquals('HTTP/1.0', $response->getProtocolVersion());
    }

    public function testCreateResponseProtocolFallback()
    {
        $factory  = new ResponseFactory();
        $response = $factory->createResponse();

        $this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
    }
}

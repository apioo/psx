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

namespace PSX\Http\Tests\Exception;

use PSX\Http\Exception\StatusCodeException;

/**
 * StatusCodeExceptionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StatusCodeExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidStatusCode()
    {
        new StatusCodeException('foo', 108);
    }

    public function testGetStatusCode()
    {
        $e = new StatusCodeException('foo', 101);

        $this->assertEquals(101, $e->getStatusCode());
    }

    public function testIsInformational()
    {
        $e = new StatusCodeException('foo', 100);

        $this->assertTrue($e->isInformational());
        $this->assertFalse($e->isSuccessful());
        $this->assertFalse($e->isRedirection());
        $this->assertFalse($e->isClientError());
        $this->assertFalse($e->isServerError());
    }

    public function testIsSuccessful()
    {
        $e = new StatusCodeException('foo', 200);

        $this->assertFalse($e->isInformational());
        $this->assertTrue($e->isSuccessful());
        $this->assertFalse($e->isRedirection());
        $this->assertFalse($e->isClientError());
        $this->assertFalse($e->isServerError());
    }

    public function testIsRedirection()
    {
        $e = new StatusCodeException('foo', 300);

        $this->assertFalse($e->isInformational());
        $this->assertFalse($e->isSuccessful());
        $this->assertTrue($e->isRedirection());
        $this->assertFalse($e->isClientError());
        $this->assertFalse($e->isServerError());
    }

    public function testIsClientError()
    {
        $e = new StatusCodeException('foo', 400);

        $this->assertFalse($e->isInformational());
        $this->assertFalse($e->isSuccessful());
        $this->assertFalse($e->isRedirection());
        $this->assertTrue($e->isClientError());
        $this->assertFalse($e->isServerError());
    }

    public function testIsServerError()
    {
        $e = new StatusCodeException('foo', 500);

        $this->assertFalse($e->isInformational());
        $this->assertFalse($e->isSuccessful());
        $this->assertFalse($e->isRedirection());
        $this->assertFalse($e->isClientError());
        $this->assertTrue($e->isServerError());
    }
}

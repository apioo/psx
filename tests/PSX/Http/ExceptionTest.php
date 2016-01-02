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

namespace PSX\Http;

/**
 * ExceptionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testBadRequestException()
    {
        $this->assertRedirection(301, 'PSX\Http\Exception\MovedPermanentlyException', array('http://google.com'));
        $this->assertRedirection(302, 'PSX\Http\Exception\FoundException', array('http://google.com'));
        $this->assertRedirection(303, 'PSX\Http\Exception\SeeOtherException', array('http://google.com'));
        $this->assertRedirection(304, 'PSX\Http\Exception\NotModifiedException');

        $this->assertClientError(400, 'PSX\Http\Exception\BadRequestException', array('foo'));
        $this->assertClientError(401, 'PSX\Http\Exception\UnauthorizedException', array('foo', 'Basic', array('realm' => 'psx')));
        $this->assertClientError(403, 'PSX\Http\Exception\ForbiddenException', array('foo'));
        $this->assertClientError(404, 'PSX\Http\Exception\NotFoundException', array('foo'));
        $this->assertClientError(405, 'PSX\Http\Exception\MethodNotAllowedException', array('foo', array('GET', 'POST')));
        $this->assertClientError(406, 'PSX\Http\Exception\NotAcceptableException', array('foo'));
        $this->assertClientError(409, 'PSX\Http\Exception\ConflictException', array('foo'));
        $this->assertClientError(410, 'PSX\Http\Exception\GoneException', array('foo'));
        $this->assertClientError(415, 'PSX\Http\Exception\UnsupportedMediaTypeException', array('foo'));

        $this->assertServerError(500, 'PSX\Http\Exception\InternalServerErrorException', array('foo'));
        $this->assertServerError(501, 'PSX\Http\Exception\NotImplementedException', array('foo'));
        $this->assertServerError(503, 'PSX\Http\Exception\ServiceUnavailableException', array('foo'));
    }

    protected function assertRedirection($statusCode, $className, array $arguments = array())
    {
        $e = $this->getException($className, $arguments);

        $this->assertInstanceOf('PSX\Http\Exception\RedirectionException', $e);
        $this->assertGreaterThanOrEqual(300, $e->getStatusCode());
        $this->assertLessThan(400, $e->getStatusCode());
        $this->assertEquals($statusCode, $e->getStatusCode());

        if ($statusCode != 304) {
            $this->assertEquals('http://google.com', $e->getLocation());
        }
    }

    protected function assertClientError($statusCode, $className, array $arguments = array())
    {
        $e = $this->getException($className, $arguments);

        $this->assertInstanceOf('PSX\Http\Exception\ClientErrorException', $e);
        $this->assertGreaterThanOrEqual(400, $e->getStatusCode());
        $this->assertLessThan(500, $e->getStatusCode());
        $this->assertEquals($statusCode, $e->getStatusCode());

        if ($statusCode == 401) {
            $this->assertEquals('Basic', $e->getType());
            $this->assertEquals(array('realm' => 'psx'), $e->getParameters());
        } elseif ($statusCode == 405) {
            $this->assertEquals(array('GET', 'POST'), $e->getAllowedMethods());
        }
    }

    protected function assertServerError($statusCode, $className, array $arguments = array())
    {
        $e = $this->getException($className, $arguments);

        $this->assertInstanceOf('PSX\Http\Exception\ServerErrorException', $e);
        $this->assertGreaterThanOrEqual(500, $e->getStatusCode());
        $this->assertLessThan(600, $e->getStatusCode());
        $this->assertEquals($statusCode, $e->getStatusCode());
    }

    protected function getException($className, array $arguments = array())
    {
        $class     = new \ReflectionClass($className);
        $exception = $class->newInstanceArgs($arguments);

        if (!$exception instanceof \Exception) {
            $this->fail('Class must be an exception');
        }

        try {
            throw $exception;
        } catch (\Exception $e) {
            return $e;
        }
    }
}

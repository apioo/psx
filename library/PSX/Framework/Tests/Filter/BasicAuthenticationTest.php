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

namespace PSX\Framework\Tests\Filter;

use PSX\Framework\Filter\BasicAuthentication;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Url;

/**
 * BasicAuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BasicAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessful()
    {
        $handle = new BasicAuthentication(function ($username, $password) {
            return $username == 'test' && $password == 'test';
        });

        $handle->onSuccess(function () {
            // success
        });

        $username = 'test';
        $password = 'test';

        $request  = new Request(new Url('http://localhost'), 'GET', array('Authorization' => 'Basic ' . base64_encode($username . ':' . $password)));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request), $this->equalTo($response));

        $handle->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\BadRequestException
     */
    public function testFailure()
    {
        $handle = new BasicAuthentication(function ($username, $password) {
            return $username == 'test' && $password == 'test';
        });

        $username = 'foo';
        $password = 'bar';

        $request  = new Request(new Url('http://localhost'), 'GET', array('Authorization' => 'Basic ' . base64_encode($username . ':' . $password)));
        $response = new Response();

        $handle->handle($request, $response, $this->getMockFilterChain());
    }

    public function testMissing()
    {
        $handle = new BasicAuthentication(function ($username, $password) {
            return $username == 'test' && $password == 'test';
        });

        $request  = new Request(new Url('http://localhost'), 'GET');
        $response = new Response();

        try {
            $handle->handle($request, $response, $this->getMockFilterChain());

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Basic', $e->getType());
            $this->assertEquals(array('realm' => 'psx'), $e->getParameters());
        }
    }

    public function testMissingWrongType()
    {
        $handle = new BasicAuthentication(function ($username, $password) {
            return $username == 'test' && $password == 'test';
        });

        $request  = new Request(new Url('http://localhost'), 'GET', array('Authorization' => 'Foo'));
        $response = new Response();

        try {
            $handle->handle($request, $response, $this->getMockFilterChain());

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Basic', $e->getType());
            $this->assertEquals(array('realm' => 'psx'), $e->getParameters());
        }
    }

    protected function getMockFilterChain()
    {
        return $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();
    }
}

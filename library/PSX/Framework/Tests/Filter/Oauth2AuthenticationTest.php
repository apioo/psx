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

use PSX\Framework\Filter\Oauth2Authentication;
use PSX\Http\Http as HttpClient;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Oauth2\Client;
use PSX\Oauth2\AccessToken;
use PSX\Uri\Url;

/**
 * Oauth2AuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Oauth2AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_TOKEN = '2YotnFZFEjr1zCsicMWpAA';

    public function testSuccessful()
    {
        $handle = new Oauth2Authentication(function ($accessToken) {

            return $accessToken == self::ACCESS_TOKEN;

        });

        $handle->onSuccess(function () {
            // success
        });

        $oauth = new Client();
        $value = $oauth->getAuthorizationHeader($this->getAccessToken());

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
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
        $handle = new Oauth2Authentication(function ($accessToken) {

            return false;

        });

        $oauth = new Client();
        $value = $oauth->getAuthorizationHeader($this->getAccessToken());

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\UnauthorizedException
     */
    public function testFailureEmptyCredentials()
    {
        $handle = new Oauth2Authentication(function ($accessToken) {
            
            return $accessToken == self::ACCESS_TOKEN;

        });

        $oauth = new Client();
        $value = '';

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    public function testMissing()
    {
        $handle = new Oauth2Authentication(function ($accessToken) {
            
            return $accessToken == self::ACCESS_TOKEN;

        });

        $oauth = new Client();
        $value = $oauth->getAuthorizationHeader($this->getAccessToken());

        $request  = new Request(new Url('http://localhost/index.php'), 'GET');
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        try {
            $handle->handle($request, $response, $filterChain);

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Bearer', $e->getType());
            $this->assertEquals(array('realm' => 'psx'), $e->getParameters());
        }
    }

    public function testMissingWrongType()
    {
        $handle = new Oauth2Authentication(function ($accessToken) {
            
            return $accessToken == self::ACCESS_TOKEN;

        });

        $oauth = new Client();
        $value = $oauth->getAuthorizationHeader($this->getAccessToken());

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Foo'));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        try {
            $handle->handle($request, $response, $filterChain);

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Bearer', $e->getType());
            $this->assertEquals(array('realm' => 'psx'), $e->getParameters());
        }
    }

    protected function getAccessToken()
    {
        $accessToken = new AccessToken();
        $accessToken->setAccessToken('2YotnFZFEjr1zCsicMWpAA');
        $accessToken->setTokenType('bearer');
        $accessToken->setExpiresIn(3600);
        $accessToken->setRefreshToken('tGzv3JOkF0XG5Qx2TlKWIA');

        return $accessToken;
    }

    protected function getMockFilterChain()
    {
        return $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();
    }
}

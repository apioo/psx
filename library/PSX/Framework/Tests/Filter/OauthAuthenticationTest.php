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

use PSX\Framework\Filter\OauthAuthentication;
use PSX\Http\Client;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Oauth\Consumer;
use PSX\Oauth\Provider\Data\Credentials;
use PSX\Uri\Url;

/**
 * OauthAuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OauthAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    const CONSUMER_KEY    = 'dpf43f3p2l4k3l03';
    const CONSUMER_SECRET = 'kd94hf93k423kf44';
    const TOKEN           = 'hh5s93j4hdidpola';
    const TOKEN_SECRET    = 'hdhd0244k9j7ao03';

    public function testSuccessful()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $handle->onSuccess(function () {
            // success
        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

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
    public function testFailureEmptyCredentials()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), '', '', '', '');

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\BadRequestException
     */
    public function testFailureWrongConsumerKey()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), 'foobar', self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\BadRequestException
     */
    public function testFailureWrongConsumerSecret()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, 'foobar', self::TOKEN, self::TOKEN_SECRET);

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\BadRequestException
     */
    public function testFailureWrongToken()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, 'foobar', self::TOKEN_SECRET);

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\BadRequestException
     */
    public function testFailureWrongTokenSecret()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, 'foobar');

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    public function testMissing()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }

        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

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
            $this->assertEquals('Oauth', $e->getType());
            $this->assertEquals(array('realm' => 'psx'), $e->getParameters());
        }
    }

    public function testMissingWrongType()
    {
        $handle = new OauthAuthentication(function ($consumerKey, $token) {

            if ($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN) {
                return new Credentials(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
            }
            
        });

        $oauth = new Consumer(new Client());
        $value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

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
            $this->assertEquals('Oauth', $e->getType());
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

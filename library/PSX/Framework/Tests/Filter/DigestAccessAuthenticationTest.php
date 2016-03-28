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

use PSX\Data\Record\Store\Memory;
use PSX\Data\Record\StoreInterface;
use PSX\Framework\Filter\DigestAccessAuthentication;
use PSX\Http\Authentication;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Url;

/**
 * DigestAccessAuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DigestAccessAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessful()
    {
        $store  = new Memory();
        $handle = $this->makeHandshake($store);

        $handle->onSuccess(function () {
            // success
        });

        $username = 'test';
        $password = 'test';

        $nonce    = $store->load('digest')->getNonce();
        $opaque   = $store->load('digest')->getOpaque();
        $cnonce   = md5(uniqid());
        $nc       = '00000001';
        $ha1      = md5($username . ':psx:' . $password);
        $ha2      = md5('GET:/index.php');
        $response = md5($ha1 . ':' . $nonce . ':' . $nc . ':' . $cnonce . ':auth:' . $ha2);

        $params = array(
            'username' => $username,
            'realm'    => 'psx',
            'nonce'    => $nonce,
            'qop'      => 'auth',
            'nc'       => $nc,
            'cnonce'   => $cnonce,
            'response' => $response,
            'opaque'   => $opaque,
        );

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Digest ' . Authentication::encodeParameters($params)));
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
        $store  = new Memory();
        $handle = $this->makeHandshake($store);

        $username = 'test';
        $password = 'bar';

        $nonce    = $store->load('digest')->getNonce();
        $opaque   = $store->load('digest')->getOpaque();
        $cnonce   = md5(uniqid());
        $nc       = '00000001';
        $ha1      = md5($username . ':psx:' . $password);
        $ha2      = md5('GET:/index.php');
        $response = md5($ha1 . ':' . $nonce . ':' . $nc . ':' . $cnonce . ':auth:' . $ha2);

        $params = array(
            'username' => $username,
            'realm'    => 'psx',
            'nonce'    => $nonce,
            'qop'      => 'auth',
            'nc'       => $nc,
            'cnonce'   => $cnonce,
            'response' => $response,
            'opaque'   => $opaque,
        );

        $request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Digest ' . Authentication::encodeParameters($params)));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        $handle->handle($request, $response, $filterChain);
    }

    public function testMissing()
    {
        $store  = new Memory();
        $handle = new DigestAccessAuthentication(function ($username) {
            return md5($username . ':psx:test');
        }, $store);

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
            $this->assertEquals('Digest', $e->getType());

            $params = $e->getParameters();

            $this->assertEquals('auth,auth-int', $params['qop']);
            $this->assertTrue(strlen($params['nonce']) > 8);
            $this->assertTrue(strlen($params['opaque']) > 8);
        }
    }

    public function testMissingWrongType()
    {
        $store  = new Memory();
        $handle = new DigestAccessAuthentication(function ($username) {
            return md5($username . ':psx:test');
        }, $store);

        $request  = new Request(new Url('http://localhost'), 'GET', array('Authorization' => 'Foo'));
        $response = new Response();

        $filterChain = $this->getMockFilterChain();
        $filterChain->expects($this->never())
            ->method('handle');

        try {
            $handle->handle($request, $response, $filterChain);

            $this->fail('Must throw an Exception');
        } catch (UnauthorizedException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Digest', $e->getType());

            $params = $e->getParameters();

            $this->assertEquals('auth,auth-int', $params['qop']);
            $this->assertTrue(strlen($params['nonce']) > 8);
            $this->assertTrue(strlen($params['opaque']) > 8);
        }
    }

    protected function makeHandshake(StoreInterface $store)
    {
        // first we make an normal request without authentication then we should
        // get an 401 response with the nonce and opaque then we can make an
        // authentication request
        $handle = new DigestAccessAuthentication(function ($username) {
            return md5($username . ':psx:test');
        }, $store);

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
            $this->assertEquals('Digest', $e->getType());

            $params = $e->getParameters();

            $this->assertEquals('auth,auth-int', $params['qop']);
            $this->assertTrue(strlen($params['nonce']) > 8);
            $this->assertTrue(strlen($params['opaque']) > 8);
        }

        // load digest from store
        $handle->loadDigest();

        return $handle;
    }

    protected function getMockFilterChain()
    {
        return $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();
    }
}

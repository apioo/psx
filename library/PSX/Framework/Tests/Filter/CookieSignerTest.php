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

use PSX\Framework\Filter\CookieSigner;
use PSX\Framework\Filter\FilterChain;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Url;

/**
 * CookieSignerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CookieSignerTest extends \PHPUnit_Framework_TestCase
{
    public function testEncryptCookie()
    {
        $request  = new Request(new Url('http://localhost'), 'GET');
        $response = new Response();

        $filters = array();
        $filters[] = new CookieSigner('secret_key');
        $filters[] = function ($request, $response, $filterChain) {

            $request->setAttribute(CookieSigner::COOKIE_NAME, array('foo' => 'data'));

            $filterChain->handle($request, $response);

        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $this->assertEquals('psx_cookie=eyJmb28iOiJkYXRhIn0=.2IwNbitA6b1VccgR1pGVIRzro4AwYN1IqNXvNHYebog=', $response->getHeader('Set-Cookie'));
    }

    public function testDecryptCookie()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array(
            'Cookie' => 'psx_cookie=eyJmb28iOiJkYXRhIn0=.2IwNbitA6b1VccgR1pGVIRzro4AwYN1IqNXvNHYebog=')
        );
        $response = new Response();

        $filters = array();
        $filters[] = new CookieSigner('secret_key');
        $filters[] = function ($request, $response, $filterChain) {

            $this->assertEquals(array('foo' => 'data'), $request->getAttribute(CookieSigner::COOKIE_NAME));

            $filterChain->handle($request, $response);

        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $this->assertEmpty($response->getHeader('Set-Cookie'));
    }

    public function testDecryptModifiedPayload()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array(
            'Cookie' => 'psx_cookie=eyJiYXIiOiJkYXRhIn0=.2IwNbitA6b1VccgR1pGVIRzro4AwYN1IqNXvNHYebog=')
        );
        $response = new Response();

        $filters = array();
        $filters[] = new CookieSigner('secret_key');
        $filters[] = function ($request, $response, $filterChain) {

            $this->assertEmpty($request->getAttribute(CookieSigner::COOKIE_NAME));

            $filterChain->handle($request, $response);

        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $this->assertEmpty($response->getHeader('Set-Cookie'));
    }

    public function testDecryptModifiedSignature()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array(
            'Cookie' => 'psx_cookie=eyJzZWNyZXQiOiJkYXRhIn0=.5+5rtZGLqN/2w4zUxPZ3FRmVPcskwyQNlSy0n5yfcI=')
        );
        $response = new Response();

        $filters = array();
        $filters[] = new CookieSigner('secret_key');
        $filters[] = function ($request, $response, $filterChain) {

            $this->assertEmpty($request->getAttribute(CookieSigner::COOKIE_NAME));

            $filterChain->handle($request, $response);

        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $this->assertEmpty($response->getHeader('Set-Cookie'));
    }

    public function testDecryptCookieDataChanged()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array(
            'Cookie' => 'psx_cookie=eyJmb28iOiJkYXRhIn0=.2IwNbitA6b1VccgR1pGVIRzro4AwYN1IqNXvNHYebog=')
        );
        $response = new Response();

        $filters = array();
        $filters[] = new CookieSigner('secret_key');
        $filters[] = function ($request, $response, $filterChain) {

            $this->assertEquals(array('foo' => 'data'), $request->getAttribute(CookieSigner::COOKIE_NAME));

            $request->setAttribute(CookieSigner::COOKIE_NAME, array('foo' => 'foo'));

            $filterChain->handle($request, $response);

        };

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);

        $this->assertEquals('psx_cookie=eyJmb28iOiJmb28ifQ==.lUT+fW1P+JlRv1+v0MtN7mQ9cx/OK5Jevt3wn/HXsj0=', $response->getHeader('Set-Cookie'));
        $this->assertEquals(array('foo' => 'foo'), $request->getAttribute(CookieSigner::COOKIE_NAME));
    }
}

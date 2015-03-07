<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Dispatch\Filter;

use PSX\Dispatch\FilterChain;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Url;

/**
 * CookieEncryptionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CookieEncryptionTest extends \PHPUnit_Framework_TestCase
{
	public function testEncryptCookie()
	{
		$request  = new Request(new Url('http://localhost'), 'GET');
		$response = new Response();

		$filters = array();
		$filters[] = new CookieEncryption('secret_key');
		$filters[] = function($request, $response, $filterChain){

			$request->setAttribute(CookieEncryption::COOKIE_NAME, array('secret' => 'data'));

			$filterChain->handle($request, $response);

		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$this->assertEquals('psx_cookie=eyJzZWNyZXQiOiJkYXRhIn0=.5+5rtZGLqN/2w4zUxPZ2FRmVPcskvwyQNlSy0n5yfcI=', $response->getHeader('Set-Cookie'));
	}

	public function testDecryptCookie()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Cookie' => 'psx_cookie=eyJzZWNyZXQiOiJkYXRhIn0=.5+5rtZGLqN/2w4zUxPZ2FRmVPcskvwyQNlSy0n5yfcI=')
		);
		$response = new Response();

		$filters = array();
		$filters[] = new CookieEncryption('secret_key');
		$filters[] = function($request, $response, $filterChain){

			$this->assertEquals(array('secret' => 'data'), $request->getAttribute(CookieEncryption::COOKIE_NAME));

			$filterChain->handle($request, $response);

		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$this->assertEmpty($response->getHeader('Set-Cookie'));
	}

	public function testDecryptModifiedPayload()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Cookie' => 'psx_cookie=eyJzZWNyZXQiOJkYXRhIn0=.5+5rtZGLqN/2w4zUxPZ2FRmVPcskvwyQNlSy0n5yfcI=')
		);
		$response = new Response();

		$filters = array();
		$filters[] = new CookieEncryption('secret_key');
		$filters[] = function($request, $response, $filterChain){

			$this->assertEmpty($request->getAttribute(CookieEncryption::COOKIE_NAME));

			$filterChain->handle($request, $response);

		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$this->assertEmpty($response->getHeader('Set-Cookie'));
	}

	public function testDecryptModifiedSignature()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Cookie' => 'psx_cookie=eyJzZWNyZXQiOiJkYXRhIn0=.5+5rtZGLqN/2w4zUxPZ2FRmVPcskwyQNlSy0n5yfcI=')
		);
		$response = new Response();

		$filters = array();
		$filters[] = new CookieEncryption('secret_key');
		$filters[] = function($request, $response, $filterChain){

			$this->assertEmpty($request->getAttribute(CookieEncryption::COOKIE_NAME));

			$filterChain->handle($request, $response);

		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$this->assertEmpty($response->getHeader('Set-Cookie'));
	}

	public function testDecryptCookieDataChanged()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Cookie' => 'psx_cookie=eyJzZWNyZXQiOiJkYXRhIn0=.5+5rtZGLqN/2w4zUxPZ2FRmVPcskvwyQNlSy0n5yfcI=')
		);
		$response = new Response();

		$filters = array();
		$filters[] = new CookieEncryption('secret_key');
		$filters[] = function($request, $response, $filterChain){

			$this->assertEquals(array('secret' => 'data'), $request->getAttribute(CookieEncryption::COOKIE_NAME));

			$request->setAttribute(CookieEncryption::COOKIE_NAME, array('secret' => 'foo'));

			$filterChain->handle($request, $response);

		};

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$this->assertEquals('psx_cookie=eyJzZWNyZXQiOiJmb28ifQ==.svZSGy320yFVfClv0HH6HP+QNtZx4Ipl6Xwg3BXlcx0=', $response->getHeader('Set-Cookie'));
	}

}

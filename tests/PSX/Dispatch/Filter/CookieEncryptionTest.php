<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

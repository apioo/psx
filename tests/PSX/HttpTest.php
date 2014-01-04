<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Http\CookieStore;
use PSX\Http\Handler;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Http\DeleteRequest;
use PSX\Http\GetRequest;
use PSX\Http\HeadRequest;
use PSX\Http\PostRequest;
use PSX\Http\PutRequest;

/**
 * HttpTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
	const URL = 'http://test.phpsx.org/http';

	private $http;

	protected function setUp()
	{
		$handler = $this->getHandler();

		$this->http = new Http($handler);
	}

	protected function tearDown()
	{
	}

	protected function getHandler()
	{
		return new Handler\Curl();
	}

	public function testDeleteRequest()
	{
		$request  = new DeleteRequest(new Url(self::URL . '/delete'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testGetRequest()
	{
		$request  = new GetRequest(new Url(self::URL . '/get'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testHeadRequest()
	{
		$request  = new HeadRequest(new Url(self::URL . '/head'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('', $response->getBody()); // must be empty
	}

	public function testPostRequest()
	{
		$request  = new PostRequest(new Url(self::URL . '/post'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testPutRequest()
	{
		$request  = new PutRequest(new Url(self::URL . '/put'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testHttpsGetRequest()
	{
		$request  = new GetRequest(new Url('https://www.google.com/accounts/ServiceLogin'));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(true, strlen($response->getBody()) > 1024);
	}

	public function testHttpChunkedTransferEncoding()
	{
		/*
		$request  = new GetRequest(new Url('http://yahoo.com'));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(true, strlen($response->getBody()) > 4096);
		*/
	}

	public function testGetRedirect()
	{
		$request  = new GetRequest(new Url('http://test.phpsx.org/http/redirect'));
		$request->setFollowLocation(true);

		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testCookieStore()
	{
		$store = new CookieStore\Memory();
		$http  = new Http(new Handler\Callback(function($request){
			return <<<TEXT
HTTP/1.1 200 OK
Content-Encoding: gzip
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT
ETag: "815832758"
Set-Cookie: webmaker.sid=s%3Aj%3A%7B%22_csrfSecret%22%3A%22uMs5W0M2tR2ewHNiJQye7lpe%22%7D.wSMQqQeiDgatt0Smv2Nbq5g92lX04%2FmOBiiRdPZIuro; Path=/; Expires=Tue, 04 Feb 2014 18:19:45 GMT; HttpOnly; Secure
Strict-Transport-Security: max-age=15768000
Vary: Accept-Encoding
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
transfer-encoding: chunked
Connection: keep-alive

foobar
TEXT;
		}));

		$http->setCookieStore($store);

		$request  = new GetRequest(new Url('http://localhost.com'));
		$response = $http->request($request);

		$cookies = $store->load('localhost.com');

		$this->assertTrue(isset($cookies['webmaker.sid']));
		$this->assertEquals('webmaker.sid', $cookies['webmaker.sid']->getName());
		$this->assertEquals('s%3Aj%3A%7B%22_csrfSecret%22%3A%22uMs5W0M2tR2ewHNiJQye7lpe%22%7D.wSMQqQeiDgatt0Smv2Nbq5g92lX04%2FmOBiiRdPZIuro', $cookies['webmaker.sid']->getValue());
		$this->assertEquals(new \DateTime('Tue, 04 Feb 2014 18:19:45 GMT'), $cookies['webmaker.sid']->getExpires());
		$this->assertEquals('/', $cookies['webmaker.sid']->getPath());
		$this->assertEquals(null, $cookies['webmaker.sid']->getDomain());
		$this->assertEquals(true, $cookies['webmaker.sid']->getSecure());
		$this->assertEquals(true, $cookies['webmaker.sid']->getHttpOnly());

		// now we have stored the cookie we check whether we get it on the next 
		// request
		$testCase = $this;
		$http  = new Http(new Handler\Callback(function($request) use ($testCase){

			$cookie = $request->getHeader('Cookie');
			$testCase->assertEquals('webmaker.sid=s%3Aj%3A%7B%22_csrfSecret%22%3A%22uMs5W0M2tR2ewHNiJQye7lpe%22%7D.wSMQqQeiDgatt0Smv2Nbq5g92lX04%2FmOBiiRdPZIuro', $cookie);

			$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

foobar
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$http->setCookieStore($store);

		$request  = new GetRequest(new Url('http://localhost.com'));
		$response = $http->request($request);
	}
}


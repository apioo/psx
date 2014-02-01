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
use PSX\Http\GetRequest;

/**
 * HttpTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
	public function testCookieStore()
	{
		$store = new CookieStore\Memory();
		$http  = new Http(new Handler\Callback(function($request){

			$response = <<<TEXT
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

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$http->setCookieStore($store);

		$request  = new GetRequest(new Url('http://localhost.com'));
		$response = $http->request($request);
		$cookies  = $store->load('localhost.com');

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
		$http     = new Http(new Handler\Callback(function($request) use ($testCase){

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


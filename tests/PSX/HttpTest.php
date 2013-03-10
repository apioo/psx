<?php
/*
 *  $Id: HttpTest.php 579 2012-08-14 18:22:10Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Http\Handler;
use PSX\Http\DeleteRequest;
use PSX\Http\GetRequest;
use PSX\Http\HeadRequest;
use PSX\Http\PostRequest;
use PSX\Http\PutRequest;

/**
 * PSX_HttpTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 579 $
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
		$request  = new GetRequest(new Url('http://yahoo.com'));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(true, strlen($response->getBody()) > 4096);
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
}


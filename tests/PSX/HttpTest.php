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

/**
 * PSX_HttpTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 579 $
 */
class PSX_HttpTest extends PHPUnit_Framework_TestCase
{
	const URL = 'http://test.phpsx.org/http';

	private $http;

	protected function setUp()
	{
		$handler = $this->getHandler();

		$this->http = new PSX_Http($handler);
	}

	protected function tearDown()
	{
	}

	protected function getHandler()
	{
		return new PSX_Http_Handler_Curl();
	}

	public function testDeleteRequest()
	{
		$request  = new PSX_Http_DeleteRequest(new PSX_Url(PSX_HttpTest::URL . '/delete'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testGetRequest()
	{
		$request  = new PSX_Http_GetRequest(new PSX_Url(PSX_HttpTest::URL . '/get'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testHeadRequest()
	{
		$request  = new PSX_Http_HeadRequest(new PSX_Url(PSX_HttpTest::URL . '/head'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('', $response->getBody()); // must be empty
	}

	public function testPostRequest()
	{
		$request  = new PSX_Http_PostRequest(new PSX_Url(PSX_HttpTest::URL . '/post'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testPutRequest()
	{
		$request  = new PSX_Http_PutRequest(new PSX_Url(PSX_HttpTest::URL . '/put'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testHttpsGetRequest()
	{
		$request  = new PSX_Http_GetRequest(new PSX_Url('https://www.google.com/accounts/ServiceLogin'));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(true, strlen($response->getBody()) > 1024);
	}

	public function testHttpChunkedTransferEncoding()
	{
		$request  = new PSX_Http_GetRequest(new PSX_Url('http://yahoo.com'));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(true, strlen($response->getBody()) > 4096);
	}

	public function testGetRedirect()
	{
		$request  = new PSX_Http_GetRequest(new PSX_Url('http://test.phpsx.org/http/redirect'));
		$request->setFollowLocation(true);

		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('step2', $response->getBody());
	}
}


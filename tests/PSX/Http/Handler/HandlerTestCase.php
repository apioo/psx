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

namespace PSX\Http\Handler;

use PSX\Http;
use PSX\Http\DeleteRequest;
use PSX\Http\GetRequest;
use PSX\Http\HeadRequest;
use PSX\Http\PostRequest;
use PSX\Http\PutRequest;
use PSX\Http\Stream\TempStream;
use PSX\Url;
use PSX\Json;

/**
 * HandlerTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class HandlerTestCase extends \PHPUnit_Framework_TestCase
{
	const URL = 'http://127.0.0.1:8000';

	protected static $isConnected;

	protected $http;

	protected function setUp()
	{
		if(self::$isConnected === null)
		{
			$handle = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 3);

			if($handle)
			{
				fwrite($handle, 'HEAD / HTTP/1.1' . "\r\n\r\n");
				fclose($handle);

				self::$isConnected = true;
			}
			else
			{
				self::$isConnected = false;
			}
		}

		if(!self::$isConnected)
		{
			$this->markTestSkipped('Local test webserver is not started');
		}

		$this->http = new Http($this->getHandler());
	}

	protected function tearDown()
	{
	}

	/**
	 * Returns the handler which gets tested
	 *
	 * @return PSX\Http\HandlerInterface
	 */
	abstract protected function getHandler();

	public function testHeadRequest()
	{
		$request  = new HeadRequest(new Url(self::URL . '/head'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());
		$this->assertEquals(null, $response->getBody());
	}

	public function testGetRequest()
	{
		$request  = new GetRequest(new Url(self::URL . '/get'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'GET'), $body);
	}

	public function testPostRequest()
	{
		$request  = new PostRequest(new Url(self::URL . '/post'), array('Content-Type' => 'text/plain'), 'foobar');
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'POST', 'request' => 'foobar'), $body);
	}

	public function testPostRequestStream()
	{
		$file     = 'tests/PSX/Template/files/foo.tpl';
		$request  = new PostRequest(new Url(self::URL . '/post'), array('Content-Type' => 'text/plain', 'Content-Length' => filesize($file)), new TempStream(fopen($file, 'r+')));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'POST', 'request' => 'Hello <?php echo $foo; ?>'), $body);
	}

	public function testPutRequest()
	{
		$request  = new PutRequest(new Url(self::URL . '/put'), array('Content-Type' => 'text/plain'), 'foobar');
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'PUT', 'request' => 'foobar'), $body);
	}

	public function testPutRequestStream()
	{
		$file     = 'tests/PSX/Template/files/foo.tpl';
		$request  = new PutRequest(new Url(self::URL . '/put'), array('Content-Type' => 'text/plain', 'Content-Length' => filesize($file)), new TempStream(fopen($file, 'r+')));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'PUT', 'request' => 'Hello <?php echo $foo; ?>'), $body);
	}

	public function testDeleteRequest()
	{
		$request  = new DeleteRequest(new Url(self::URL . '/delete'), array('Content-Type' => 'text/plain'), 'foobar');
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'DELETE', 'request' => 'foobar'), $body);
	}

	public function testDeleteRequestStream()
	{
		$file     = 'tests/PSX/Template/files/foo.tpl';
		$request  = new DeleteRequest(new Url(self::URL . '/delete'), array('Content-Type' => 'text/plain', 'Content-Length' => filesize($file)), new TempStream(fopen($file, 'r+')));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'DELETE', 'request' => 'Hello <?php echo $foo; ?>'), $body);
	}

	public function testFollowRedirects()
	{
		$request  = new GetRequest(new Url(self::URL . '/redirect'));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = Json::decode((string) $response->getBody());

		$this->assertEquals(array('success' => true, 'method' => 'GET'), $body);
	}

	/**
	 * @expectedException \PSX\Http\RedirectException
	 */
	public function testMaxRedirect()
	{
		$request = new GetRequest(new Url(self::URL . '/redirect'));
		$request->setFollowLocation(true, 1);

		$response = $this->http->request($request);
	}

	/**
	 * The bigdata endpoint returns an 4mb response of full stops. We take 
	 * advantage of streaming and read only the first 8 bytes of the response
	 */
	public function testReadBigData()
	{
		$request  = new GetRequest(new Url(self::URL . '/bigdata'));
		$response = $this->http->request($request);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());

		$body = $response->getBody()->read(8);

		$response->getBody()->close();

		$this->assertEquals('........', $body);
	}
}

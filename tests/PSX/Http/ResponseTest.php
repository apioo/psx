<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http;

use PSX\Http;
use PSX\Http\Stream\StringStream;

/**
 * ResponseTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
	public function testGetCharset()
	{
		// normal charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::parse($httpResponse);

		$this->assertEquals('UTF-8', $response->getCharset());

		// lowercase charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=utf-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::parse($httpResponse);

		$this->assertEquals('UTF-8', $response->getCharset());

		// unknown charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=foo' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::parse($httpResponse);

		$this->assertEquals('FOO', $response->getCharset());

		// no charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::parse($httpResponse);

		$this->assertEquals(false, $response->getCharset());
	}

	public function testGetBodyAsString()
	{
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;
		$httpResponse.= chr(0xE2) . chr(0x82) . chr(0xAC);

		$response = Response::parse($httpResponse);

		$this->assertEquals('€', $response->getBodyAsString());
		$this->assertEquals('EUR', $response->getBodyAsString('ISO-8859-1//TRANSLIT'));
	}

	public function testToString()
	{
		$body = new StringStream();
		$body->write('foobar');

		$response = new Response('HTTP/1.1', 200, 'OK');
		$response->setHeader('Content-Type', 'text/html; charset=UTF-8');
		$response->setBody($body);

		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;
		$httpResponse.= 'foobar';

		$this->assertEquals($httpResponse, (string) $response);
	}

	public function testParse()
	{
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;
		$httpResponse.= 'foobar';

		$response = Response::parse($httpResponse);

		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());
		$this->assertEquals('text/html; charset=UTF-8', (string) $response->getHeader('Content-Type'));
		$this->assertEquals('foobar', $response->getBody());
		$this->assertEquals('UTF-8', $response->getCharset());
	}
}

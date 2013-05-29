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

namespace PSX\Http;

use PSX\Http;

/**
 * ResponseTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testResponse()
	{
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;
		$httpResponse.= 'foobar';

		$response = Response::convert($httpResponse);

		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals('foobar', $response->getBody());
		$this->assertEquals('UTF-8', $response->getCharset());

		$header = $response->getHeader();

		$this->assertEquals('text/html; charset=UTF-8', $header['content-type']);
	}

	public function testGetCharset()
	{
		// normal charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::convert($httpResponse);

		$this->assertEquals('UTF-8', $response->getCharset());

		// lowercase charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=utf-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::convert($httpResponse);

		$this->assertEquals('UTF-8', $response->getCharset());

		// unknown charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=foo' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::convert($httpResponse);

		$this->assertEquals('FOO', $response->getCharset());

		// no charset
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html' . Http::$newLine;
		$httpResponse.= Http::$newLine;

		$response = Response::convert($httpResponse);

		$this->assertEquals(false, $response->getCharset());
	}

	public function testGetBodyAsString()
	{
		$httpResponse = 'HTTP/1.1 200 OK' . Http::$newLine;
		$httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpResponse.= Http::$newLine;
		$httpResponse.= chr(0xE2) . chr(0x82) . chr(0xAC);

		$response = Response::convert($httpResponse);

		$this->assertEquals('€', $response->getBodyAsString());
		$this->assertEquals('', $response->getBodyAsString('ISO-8859-1//IGNORE'));
	}
}

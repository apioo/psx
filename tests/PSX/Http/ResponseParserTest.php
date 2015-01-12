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

/**
 * ResponseParserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseParserTest extends \PHPUnit_Framework_TestCase
{
	public function testParseStrictMode()
	{
		$response = 'HTTP/1.1 200 OK' . Http::$newLine;
		$response.= 'Vary: Accept-Encoding' . Http::$newLine;
		$response.= 'Content-Type: text/plain' . Http::$newLine;
		$response.= 'Last-Modified: Mon, 02 Apr 2012 02:13:37 GMT' . Http::$newLine;
		$response.= 'Date: Sat, 07 Dec 2013 13:27:33 GMT' . Http::$newLine;
		$response.= 'Expires: Sat, 07 Dec 2013 13:27:33 GMT' . Http::$newLine;
		$response.= 'Cache-Control: public, max-age=0' . Http::$newLine;
		$response.= 'X-Content-Type-Options: nosniff' . Http::$newLine;
		$response.= 'Server: sffe' . Http::$newLine;
		$response.= 'X-XSS-Protection: 1; mode=block' . Http::$newLine;
		$response.= 'Alternate-Protocol: 80:quic' . Http::$newLine;
		$response.= 'Transfer-Encoding: chunked' . Http::$newLine;
		$response.= Http::$newLine;
		$response.= 'Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.';

		$parser = new ResponseParser(ResponseParser::MODE_STRICT);

		$response = $parser->parse($response);

		$this->assertInstanceOf('PSX\Http\Response', $response);
		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());
		$this->assertEquals(array(
			'content-type'           => ['text/plain'],
			'date'                   => ['Sat, 07 Dec 2013 13:27:33 GMT'],
			'vary'                   => ['Accept-Encoding'],
			'last-modified'          => ['Mon, 02 Apr 2012 02:13:37 GMT'],
			'expires'                => ['Sat, 07 Dec 2013 13:27:33 GMT'],
			'cache-control'          => ['public, max-age=0'],
			'x-content-type-options' => ['nosniff'],
			'server'                 => ['sffe'],
			'x-xss-protection'       => ['1; mode=block'],
			'alternate-protocol'     => ['80:quic'],
			'transfer-encoding'      => ['chunked'],
		), $response->getHeaders());
		$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.', $response->getBody());
	}

	public function testParseLooseMode()
	{
		$parser = new ResponseParser(ResponseParser::MODE_LOOSE);
		$seperators = array("\r\n", "\n", "\r");

		foreach($seperators as $newline)
		{
			$response = 'HTTP/1.1 200 OK' . $newline;
			$response.= 'Vary: Accept-Encoding' . $newline;
			$response.= 'Content-Type: text/plain' . $newline;
			$response.= 'Last-Modified: Mon, 02 Apr 2012 02:13:37 GMT' . $newline;
			$response.= 'Date: Sat, 07 Dec 2013 13:27:33 GMT' . $newline;
			$response.= 'Expires: Sat, 07 Dec 2013 13:27:33 GMT' . $newline;
			$response.= 'Cache-Control: public, max-age=0' . $newline;
			$response.= 'X-Content-Type-Options: nosniff' . $newline;
			$response.= 'Server: sffe' . $newline;
			$response.= 'X-XSS-Protection: 1; mode=block' . $newline;
			$response.= 'Alternate-Protocol: 80:quic' . $newline;
			$response.= 'Transfer-Encoding: chunked' . $newline;
			$response.= $newline;
			$response.= 'Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.';

			$response = $parser->parse($response);

			$this->assertInstanceOf('PSX\Http\Response', $response);
			$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
			$this->assertEquals(200, $response->getStatusCode());
			$this->assertEquals('OK', $response->getReasonPhrase());
			$this->assertEquals(array(
				'content-type'           => ['text/plain'],
				'date'                   => ['Sat, 07 Dec 2013 13:27:33 GMT'],
				'vary'                   => ['Accept-Encoding'],
				'last-modified'          => ['Mon, 02 Apr 2012 02:13:37 GMT'],
				'expires'                => ['Sat, 07 Dec 2013 13:27:33 GMT'],
				'cache-control'          => ['public, max-age=0'],
				'x-content-type-options' => ['nosniff'],
				'server'                 => ['sffe'],
				'x-xss-protection'       => ['1; mode=block'],
				'alternate-protocol'     => ['80:quic'],
				'transfer-encoding'      => ['chunked'],
			), $response->getHeaders());
			$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.', $response->getBody());
		}
	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testParseInvalidStatusLine()
	{
		$response = 'foobar' . Http::$newLine;
		$response.= 'Vary: Accept-Encoding' . Http::$newLine;

		$parser = new ResponseParser(ResponseParser::MODE_STRICT);
		$parser->parse($response);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testParseEmpty()
	{
		$response = '';

		$parser = new ResponseParser(ResponseParser::MODE_STRICT);
		$parser->parse($response);
	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testParseNoLineEnding()
	{
		$response = 'HTTP/1.1 200 OK';
		$response.= 'Vary: Accept-Encoding';

		$parser = new ResponseParser(ResponseParser::MODE_STRICT);
		$parser->parse($response);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testParseInvalidMode()
	{
		$response = 'HTTP/1.1 200 OK' . Http::$newLine;
		$response.= 'Vary: Accept-Encoding' . Http::$newLine;

		$parser = new ResponseParser('foo');
		$parser->parse($response);
	}

	public function testBuildResponseFromHeader()
	{
		$response = ResponseParser::buildResponseFromHeader(array(
			'HTTP/1.1 200 OK',
			'Vary: Accept-Encoding',
			'Content-Type: text/plain',
			'Last-Modified: Mon, 02 Apr 2012 02:13:37 GMT',
			'Date: Sat, 07 Dec 2013 13:27:33 GMT',
			'Expires: Sat, 07 Dec 2013 13:27:33 GMT',
			'Cache-Control: public, max-age=0',
			'X-Content-Type-Options: nosniff',
			'Server: sffe',
			'X-XSS-Protection: 1; mode=block',
			'Alternate-Protocol: 80:quic',
			'Transfer-Encoding: chunked',
		));

		$this->assertInstanceOf('PSX\Http\Response', $response);
		$this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('OK', $response->getReasonPhrase());
		$this->assertEquals(array(
			'content-type'           => ['text/plain'],
			'date'                   => ['Sat, 07 Dec 2013 13:27:33 GMT'],
			'vary'                   => ['Accept-Encoding'],
			'last-modified'          => ['Mon, 02 Apr 2012 02:13:37 GMT'],
			'expires'                => ['Sat, 07 Dec 2013 13:27:33 GMT'],
			'cache-control'          => ['public, max-age=0'],
			'x-content-type-options' => ['nosniff'],
			'server'                 => ['sffe'],
			'x-xss-protection'       => ['1; mode=block'],
			'alternate-protocol'     => ['80:quic'],
			'transfer-encoding'      => ['chunked'],
		), $response->getHeaders());
	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testBuildResponseFromHeaderInvalidStatusLine()
	{
		ResponseParser::buildResponseFromHeader(array(
			'foobar',
			'Vary: Accept-Encoding',
		));
	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testBuildResponseFromHeaderEmpty()
	{
		ResponseParser::buildResponseFromHeader(array());
	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testBuildResponseFromHeaderEmptyStatusLine()
	{
		ResponseParser::buildResponseFromHeader(array(
			'',
			'Vary: Accept-Encoding',
		));
	}
}

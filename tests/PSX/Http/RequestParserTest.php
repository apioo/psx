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
use PSX\Url;

/**
 * RequestParserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestParserTest extends \PHPUnit_Framework_TestCase
{
	public function testParseStrictMode()
	{
		$request = 'GET /foobar?foo=bar#fragment HTTP/1.1' . Http::$newLine;
		$request.= 'Content-Type: text/plain' . Http::$newLine;
		$request.= 'User-Agent: psx' . Http::$newLine;
		$request.= Http::$newLine;
		$request.= 'Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.';

		$parser  = new RequestParser(new Url('http://localhost.com'), RequestParser::MODE_STRICT);
		$request = $parser->parse($request);

		$this->assertInstanceOf('PSX\Http\Request', $request);
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('http://localhost.com/foobar?foo=bar#fragment', $request->getUri()->toString());
		$this->assertEquals('HTTP/1.1', $request->getProtocolVersion());
		$this->assertEquals(array(
			'content-type' => ['text/plain'],
			'user-agent'   => ['psx'],
		), $request->getHeaders());
		$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.', $request->getBody());
	}

	public function testParseLooseMode()
	{
		$parser     = new RequestParser(new Url('http://localhost.com'), RequestParser::MODE_LOOSE);
		$seperators = array("\r\n", "\n", "\r");

		foreach($seperators as $newline)
		{
			$request = 'GET /foobar?foo=bar#fragment HTTP/1.1' . $newline;
			$request.= 'Content-Type: text/plain' . $newline;
			$request.= 'User-Agent: psx' . $newline;
			$request.= $newline;
			$request.= 'Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.';

			$request = $parser->parse($request);

			$this->assertInstanceOf('PSX\Http\Request', $request);
			$this->assertEquals('GET', $request->getMethod());
			$this->assertEquals('http://localhost.com/foobar?foo=bar#fragment', $request->getUri()->toString());
			$this->assertEquals('HTTP/1.1', $request->getProtocolVersion());
			$this->assertEquals(array(
				'content-type' => ['text/plain'],
				'user-agent'   => ['psx'],
			), $request->getHeaders());
			$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.', $request->getBody());
		}
	}

	public function testParseNoBaseUrl()
	{
		$request = 'GET /foobar?foo=bar#fragment HTTP/1.1' . Http::$newLine;
		$request.= 'Content-Type: text/plain' . Http::$newLine;
		$request.= 'User-Agent: psx' . Http::$newLine;
		$request.= Http::$newLine;
		$request.= 'Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.';

		$parser  = new RequestParser();
		$request = $parser->parse($request);

		$this->assertInstanceOf('PSX\Http\Request', $request);
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('/foobar?foo=bar#fragment', $request->getUri()->toString());
		$this->assertEquals('HTTP/1.1', $request->getProtocolVersion());
		$this->assertEquals(array(
			'content-type' => ['text/plain'],
			'user-agent'   => ['psx'],
		), $request->getHeaders());
		$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.', $request->getBody());

	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testParseInvalidStatusLine()
	{
		$request = 'foobar' . Http::$newLine;
		$request.= 'Vary: Accept-Encoding' . Http::$newLine;

		$parser = new RequestParser(new Url('http://localhost.com'), RequestParser::MODE_STRICT);
		$parser->parse($request);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testParseEmpty()
	{
		$request = '';

		$parser = new RequestParser(new Url('http://localhost.com'), RequestParser::MODE_STRICT);
		$parser->parse($request);
	}

	/**
	 * @expectedException PSX\Http\ParseException
	 */
	public function testParseNoLineEnding()
	{
		$request = 'GET /foobar?foo=bar#fragment HTTP/1.1';
		$request.= 'Vary: Accept-Encoding';

		$parser = new RequestParser(new Url('http://localhost.com'), RequestParser::MODE_STRICT);
		$parser->parse($request);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testParseInvalidMode()
	{
		$request = 'GET /foobar?foo=bar#fragment HTTP/1.1' . Http::$newLine;
		$request.= 'Content-Type: text/plain' . Http::$newLine;

		$parser = new RequestParser(new Url('http://localhost.com'), 'foo');
		$parser->parse($request);
	}

	public function testConvert()
	{
		$httpRequest = 'GET /foo/bar?foo=bar#test HTTP/1.1' . Http::$newLine;
		$httpRequest.= 'Content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpRequest.= Http::$newLine;
		$httpRequest.= 'foobar';

		$request = RequestParser::convert($httpRequest, new Url('http://psx.dev'));

		$this->assertEquals('http://psx.dev/foo/bar?foo=bar#test', $request->getUri()->toString());
		$this->assertEquals('GET', $request->getMethod());
		$this->assertEquals('HTTP/1.1', $request->getProtocolVersion());
		$this->assertEquals('text/html; charset=UTF-8', (string) $request->getHeader('Content-Type'));
		$this->assertEquals('foobar', $request->getBody());
	}
}

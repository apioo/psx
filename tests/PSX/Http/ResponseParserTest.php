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
		$response = 'SFRUUC8xLjEgMjAwIE9LDQpWYXJ5OiBBY2NlcHQtRW5jb2RpbmcNCkNvbnRlbnQtVHlwZTogdGV4dC9wbGFpbg0KTGFzdC1Nb2RpZmllZDogTW9uLCAwMiBBcHIgMjAxMiAwMjoxMzozNyBHTVQNCkRhdGU6IFNhdCwgMDcgRGVjIDIwMTMgMTM6Mjc6MzMgR01UDQpFeHBpcmVzOiBTYXQsIDA3IERlYyAyMDEzIDEzOjI3OjMzIEdNVA0KQ2FjaGUtQ29udHJvbDogcHVibGljLCBtYXgtYWdlPTANClgtQ29udGVudC1UeXBlLU9wdGlvbnM6IG5vc25pZmYNClNlcnZlcjogc2ZmZQ0KWC1YU1MtUHJvdGVjdGlvbjogMTsgbW9kZT1ibG9jaw0KQWx0ZXJuYXRlLVByb3RvY29sOiA4MDpxdWljDQpUcmFuc2Zlci1FbmNvZGluZzogY2h1bmtlZA0KDQpHb29nbGUgaXMgYnVpbHQgYnkgYSBsYXJnZSB0ZWFtIG9mIGVuZ2luZWVycywgZGVzaWduZXJzLCByZXNlYXJjaGVycywgcm9ib3RzLCBhbmQgb3RoZXJzIGluIG1hbnkgZGlmZmVyZW50IHNpdGVzIGFjcm9zcyB0aGUgZ2xvYmUuIEl0IGlzIHVwZGF0ZWQgY29udGludW91c2x5LCBhbmQgYnVpbHQgd2l0aCBtb3JlIHRvb2xzIGFuZCB0ZWNobm9sb2dpZXMgdGhhbiB3ZSBjYW4gc2hha2UgYSBzdGljayBhdC4gSWYgeW91J2QgbGlrZSB0byBoZWxwIHVzIG91dCwgc2VlIGdvb2dsZS5jb20vam9icy4K';

		$parser = new ResponseParser(ResponseParser::MODE_STRICT);

		$response = $parser->parse(base64_decode($response));

		$this->assertInstanceOf('PSX\Http\Response', $response);
		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(array(
			'content-type'           => 'text/plain',
			'date'                   => 'Sat, 07 Dec 2013 13:27:33 GMT',
			'vary'                   => 'Accept-Encoding',
			'last-modified'          => 'Mon, 02 Apr 2012 02:13:37 GMT',
			'expires'                => 'Sat, 07 Dec 2013 13:27:33 GMT',
			'cache-control'          => 'public, max-age=0',
			'x-content-type-options' => 'nosniff',
			'server'                 => 'sffe',
			'x-xss-protection'       => '1; mode=block',
			'alternate-protocol'     => '80:quic',
			'transfer-encoding'      => 'chunked',
		), $response->getHeader());
		$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.', $response->getBody());
	}

	public function testParseLooseMode()
	{
		$response = <<<TEXT
HTTP/1.1 200 OK
Vary: Accept-Encoding
Content-Type: text/plain
Last-Modified: Mon, 02 Apr 2012 02:13:37 GMT
Date: Sat, 07 Dec 2013 13:27:33 GMT
Expires: Sat, 07 Dec 2013 13:27:33 GMT
Cache-Control: public, max-age=0
X-Content-Type-Options: nosniff
Server: sffe
X-XSS-Protection: 1; mode=block
Alternate-Protocol: 80:quic
Transfer-Encoding: chunked

Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you'd like to help us out, see google.com/jobs.
TEXT;

		$parser = new ResponseParser(ResponseParser::MODE_LOOSE);

		$response = $parser->parse($response);

		$this->assertInstanceOf('PSX\Http\Response', $response);
		$this->assertEquals('HTTP/1.1', $response->getScheme());
		$this->assertEquals(200, $response->getCode());
		$this->assertEquals('OK', $response->getMessage());
		$this->assertEquals(array(
			'content-type'           => 'text/plain',
			'date'                   => 'Sat, 07 Dec 2013 13:27:33 GMT',
			'vary'                   => 'Accept-Encoding',
			'last-modified'          => 'Mon, 02 Apr 2012 02:13:37 GMT',
			'expires'                => 'Sat, 07 Dec 2013 13:27:33 GMT',
			'cache-control'          => 'public, max-age=0',
			'x-content-type-options' => 'nosniff',
			'server'                 => 'sffe',
			'x-xss-protection'       => '1; mode=block',
			'alternate-protocol'     => '80:quic',
			'transfer-encoding'      => 'chunked',
		), $response->getHeader());
		$this->assertEquals('Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you\'d like to help us out, see google.com/jobs.' . "\n", $response->getBody());
	}
}

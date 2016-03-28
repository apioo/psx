<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Http\Tests;

use PSX\Http\Http;
use PSX\Http\Response;
use PSX\Http\ResponseParser;

/**
 * ResponseParserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseStrictMode()
    {
        $response = 'HTTP/1.1 200 OK' . Http::NEW_LINE;
        $response.= 'Vary: Accept-Encoding' . Http::NEW_LINE;
        $response.= 'Content-Type: text/plain' . Http::NEW_LINE;
        $response.= 'Last-Modified: Mon, 02 Apr 2012 02:13:37 GMT' . Http::NEW_LINE;
        $response.= 'Date: Sat, 07 Dec 2013 13:27:33 GMT' . Http::NEW_LINE;
        $response.= 'Expires: Sat, 07 Dec 2013 13:27:33 GMT' . Http::NEW_LINE;
        $response.= 'Cache-Control: public, max-age=0' . Http::NEW_LINE;
        $response.= 'X-Content-Type-Options: nosniff' . Http::NEW_LINE;
        $response.= 'Server: sffe' . Http::NEW_LINE;
        $response.= 'X-XSS-Protection: 1; mode=block' . Http::NEW_LINE;
        $response.= 'Alternate-Protocol: 80:quic' . Http::NEW_LINE;
        $response.= 'Transfer-Encoding: chunked' . Http::NEW_LINE;
        $response.= Http::NEW_LINE;
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

        foreach ($seperators as $newline) {
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
     * @expectedException \PSX\Http\ParseException
     */
    public function testParseInvalidStatusLine()
    {
        $response = 'foobar' . Http::NEW_LINE;
        $response.= 'Vary: Accept-Encoding' . Http::NEW_LINE;

        $parser = new ResponseParser(ResponseParser::MODE_STRICT);
        $parser->parse($response);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseEmpty()
    {
        $response = '';

        $parser = new ResponseParser(ResponseParser::MODE_STRICT);
        $parser->parse($response);
    }

    /**
     * @expectedException \PSX\Http\ParseException
     */
    public function testParseNoLineEnding()
    {
        $response = 'HTTP/1.1 200 OK';
        $response.= 'Vary: Accept-Encoding';

        $parser = new ResponseParser(ResponseParser::MODE_STRICT);
        $parser->parse($response);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseInvalidMode()
    {
        $response = 'HTTP/1.1 200 OK' . Http::NEW_LINE;
        $response.= 'Vary: Accept-Encoding' . Http::NEW_LINE;

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
     * @expectedException \PSX\Http\ParseException
     */
    public function testBuildResponseFromHeaderInvalidStatusLine()
    {
        ResponseParser::buildResponseFromHeader(array(
            'foobar',
            'Vary: Accept-Encoding',
        ));
    }

    /**
     * @expectedException \PSX\Http\ParseException
     */
    public function testBuildResponseFromHeaderEmpty()
    {
        ResponseParser::buildResponseFromHeader(array());
    }

    /**
     * @expectedException \PSX\Http\ParseException
     */
    public function testBuildResponseFromHeaderEmptyStatusLine()
    {
        ResponseParser::buildResponseFromHeader(array(
            '',
            'Vary: Accept-Encoding',
        ));
    }

    public function testBuildStatusLine()
    {
        $response = new Response(200);

        $this->assertEquals('HTTP/1.1 200 OK', ResponseParser::buildStatusLine($response));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildStatusLineUnknownStausCode()
    {
        $response = new Response(0);

        ResponseParser::buildStatusLine($response);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBuildStatusLineUnknownStausCodeWithNoReason()
    {
        $response = new Response(800);

        ResponseParser::buildStatusLine($response);
    }

    public function testBuildStatusLineUnknownStausCodeWithReason()
    {
        $response = new Response();
        $response->setStatus(800, 'Foo');

        $this->assertEquals('HTTP/1.1 800 Foo', ResponseParser::buildStatusLine($response));
    }

    public function testConvert()
    {
        $httpResponse = 'HTTP/1.1 200 OK' . Http::NEW_LINE;
        $httpResponse.= 'Content-type: text/html; charset=UTF-8' . Http::NEW_LINE;
        $httpResponse.= Http::NEW_LINE;
        $httpResponse.= 'foobar';

        $response = ResponseParser::convert($httpResponse);

        $this->assertEquals('HTTP/1.1', $response->getProtocolVersion());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals('text/html; charset=UTF-8', (string) $response->getHeader('Content-Type'));
        $this->assertEquals('foobar', $response->getBody());
    }
}

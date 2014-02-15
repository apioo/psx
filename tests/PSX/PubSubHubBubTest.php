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

use PSX\Data\Writer;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Http\Request;
use PSX\Http\Handler\Callback;
use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;

/**
 * PubSubHubBubTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PubSubHubBubTest extends \PHPUnit_Framework_TestCase
{
	public function testNotification()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$testCase->assertEquals('application/x-www-form-urlencoded', (string) $request->getHeader('Content-Type'));
			$testCase->assertEquals('hub.mode=publish&hub.url=http%3A%2F%2Fyoutube.com', (string) $request->getBody());

			$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

SUCCESS
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb     = new PubSubHubBub($http);
		$response = $pshb->notification(new Url('http://127.0.0.1/pshb'), new Url('http://youtube.com'));

		$this->assertTrue($response);
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testNotificationError()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$testCase->assertEquals('application/x-www-form-urlencoded', (string) $request->getHeader('Content-Type'));
			$testCase->assertEquals('hub.mode=publish&hub.url=http%3A%2F%2Fyoutube.com', (string) $request->getBody());

			$response = <<<TEXT
HTTP/1.1 500 Internal Server Error
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

ERROR
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb     = new PubSubHubBub($http);
		$response = $pshb->notification(new Url('http://127.0.0.1/pshb'), new Url('http://youtube.com'));
	}

	public function testRequest()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$testCase->assertEquals('application/x-www-form-urlencoded', (string) $request->getHeader('Content-Type'));
			$testCase->assertEquals('hub.callback=http%3A%2F%2F127.0.0.1%2Fcallback&hub.mode=subscribe&hub.topic=http%3A%2F%2Fyoutube.com&hub.verify=sync', (string) $request->getBody());

			$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

SUCCESS
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb     = new PubSubHubBub($http);
		$response = $pshb->request(new Url('http://127.0.0.1/pshb'), new Url('http://127.0.0.1/callback'), 'subscribe', new Url('http://youtube.com'), 'sync');

		$this->assertTrue($response);
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testRequestError()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$testCase->assertEquals('application/x-www-form-urlencoded', (string) $request->getHeader('Content-Type'));
			$testCase->assertEquals('hub.callback=http%3A%2F%2F127.0.0.1%2Fcallback&hub.mode=subscribe&hub.topic=http%3A%2F%2Fyoutube.com&hub.verify=sync', (string) $request->getBody());

			$response = <<<TEXT
HTTP/1.1 500 Internal Server Error
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

ERROR
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb     = new PubSubHubBub($http);
		$response = $pshb->request(new Url('http://127.0.0.1/pshb'), new Url('http://127.0.0.1/callback'), 'subscribe', new Url('http://youtube.com'), 'sync');
	}

	public function testDiscoverAtom()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: application/atom+xml; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<link rel="hub" href="http://127.0.0.1/pshb/hub" />
	<link rel="self" href="http://127.0.0.1/atom" />
	<updated>2008-08-11T02:15:01Z</updated>
	<entry>
		<title>Heathcliff</title>
		<link href="http://publisher.example.com/happycat25.xml" />
		<id>http://publisher.example.com/happycat25.xml</id>
		<updated>2008-08-11T02:15:01Z</updated>
		<content>What a happy cat. Full content goes here.</content>
	</entry>
</feed>
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb = new PubSubHubBub($http);
		$url  = $pshb->discover(new Url('http://127.0.0.1/atom', PubSubHubBub::ATOM));

		$this->assertInstanceOf('PSX\Url', $url);
		$this->assertEquals('http://127.0.0.1/pshb/hub', (string) $url);
	}

	public function testDiscoverRss()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: application/rss+xml; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link rel="hub" href="http://127.0.0.1/pshb/hub" />
		<atom:link rel="self" type="application/rss+xml" href="http://127.0.0.1/rss" />
		<lastBuildDate>Fri, 25 Mar 2011 03:45:43 +0000</lastBuildDate>
		<item>
			<title>Heathcliff</title>
			<link>http://publisher.example.com/happycat25.xml</link>
			<guid>http://publisher.example.com/happycat25.xml</guid>
			<pubDate>Fri, 25 Mar 2011 03:45:43 +0000</pubDate>
			<description>What a happy cat. Full content goes here.</description>
		</item>
	</channel>
</rss>
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb = new PubSubHubBub($http);
		$url  = $pshb->discover(new Url('http://127.0.0.1/rss', PubSubHubBub::RSS2));

		$this->assertInstanceOf('PSX\Url', $url);
		$this->assertEquals('http://127.0.0.1/pshb/hub', (string) $url);
	}
}


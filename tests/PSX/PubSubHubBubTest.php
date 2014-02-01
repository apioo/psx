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
	const URL_TOPIC    = 'http://test.phpsx.org/pubsubhubbub/topic';
	const URL_HUB      = 'http://test.phpsx.org/pubsubhubbub/hub';
	const URL_CALLBACK = 'http://test.phpsx.org/pubsubhubbub/callback';

	private $http;
	private $pshb;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/PubSubHubBub/pubsubhubbub_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/PubSubHubBub/pubsubhubbub_http_fixture.xml');

		$this->http = new Http($mock);
		$this->pshb = new PubSubHubBub($this->http);
	}

	protected function tearDown()
	{
		unset($this->pshb);
		unset($this->http);
	}

	public function testAtomDiscover()
	{
		$url = new Url(self::URL_TOPIC . '/atom');
		$url = $this->pshb->discover($url, PubSubHubBub::ATOM);

		$this->assertEquals(true, $url instanceof Url);
		$this->assertEquals(self::URL_HUB, (string) $url);
	}

	public function testRssDiscover()
	{
		$url = new Url(self::URL_TOPIC . '/rss');
		$url = $this->pshb->discover($url, PubSubHubBub::RSS2);

		$this->assertEquals(true, $url instanceof Url);
		$this->assertEquals(self::URL_HUB, (string) $url);
	}

	public function testInsertAtom()
	{
		$header = array('Content-type' => Writer\Atom::$mime);
		$body   = <<<FEED
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<updated>2008-08-11T02:15:01Z</updated>
	<entry>
		<title>Heathcliff</title>
		<link href="http://publisher.example.com/happycat25.xml" />
		<id>http://publisher.example.com/happycat25.xml</id>
		<updated>2008-08-11T02:15:01Z</updated>
		<content>What a happy cat. Full content goes here.</content>
	</entry>
</feed>
FEED;

		$request  = new Request(new Url(self::URL_CALLBACK), 'POST', $header, $body);
		$response = $this->http->request($request);

		$this->assertEquals(200, $response->getStatusCode(), $response->getBody());
		$this->assertEquals('INSERT ATOM Heathcliff', $response->getBody());
	}

	public function testRequest()
	{
		$testCase = $this;
		$http     = new Http(new Callback(function($request) use ($testCase){

			$testCase->assertEquals('hub.callback=http%3A%2F%2Ftest.phpsx.org%2Fpubsubhubbub%2Fcallback&hub.mode=subscribe&hub.topic=http%3A%2F%2Fyoutube.com&hub.verify=sync', $request->getBody());

			$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

foobar
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$pshb     = new PubSubHubBub($http);
		$response = $pshb->request(new Url('http://localhost.com'), new Url(self::URL_CALLBACK), 'subscribe', new Url('http://youtube.com'), 'sync');

		$this->assertTrue($response);
	}
}


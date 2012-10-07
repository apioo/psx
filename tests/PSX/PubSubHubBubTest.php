<?php
/*
 *  $Id: PubSubHubBubTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_PubSubHubBubTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_PubSubHubBubTest extends PHPUnit_Framework_TestCase
{
	const URL_TOPIC    = 'http://test.phpsx.org/pubsubhubbub/topic';
	const URL_HUB      = 'http://test.phpsx.org/pubsubhubbub/hub';
	const URL_CALLBACK = 'http://test.phpsx.org/pubsubhubbub/callback';

	private $http;
	private $pshb;

	protected function setUp()
	{
		$this->http = new PSX_Http(new PSX_Http_Handler_Curl());
		$this->pshb = new PSX_PubSubHubBub($this->http);
	}

	protected function tearDown()
	{
	}

	public function testAtomDiscover()
	{
		$url = new PSX_Url(self::URL_TOPIC . '/atom');
		$url = $this->pshb->discover($url, PSX_PubSubHubBub::ATOM);

		$this->assertEquals(true, $url instanceof PSX_Url);
		$this->assertEquals(self::URL_HUB, (string) $url);
	}

	public function testRssDiscover()
	{
		$url = new PSX_Url(self::URL_TOPIC . '/rss');
		$url = $this->pshb->discover($url, PSX_PubSubHubBub::RSS2);

		$this->assertEquals(true, $url instanceof PSX_Url);
		$this->assertEquals(self::URL_HUB, (string) $url);
	}

	public function testInsertAtom()
	{
		$header = array('Content-type' => PSX_Data_Writer_Atom::$mime);
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

		$request  = new PSX_Http_Request(new PSX_Url(self::URL_CALLBACK), 'POST', $header, $body);
		$response = $this->http->request($request);

		$this->assertEquals(200, $response->getCode(), $response->getBody());
		$this->assertEquals('INSERT ATOM Heathcliff', $response->getBody());
	}

	public function testInsertRss()
	{
		$header = array('Content-type' => PSX_Data_Writer_Rss::$mime);
		$body   = <<<FEED
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<pubDate>Sat, 07 Sep 2002 00:00:01 GMT</pubDate>
		<item>
			<title>Heathcliff</title>
			<link>http://publisher.example.com/happycat25.xml</link>
			<guid>http://publisher.example.com/happycat25.xml</guid>
			<pubDate>Sat, 07 Sep 2002 00:00:01 GMT</pubDate>
			<description>What a happy cat. Full content goes here.</description>
		</item>
	</channel>
</rss>
FEED;

		$request  = new PSX_Http_Request(new PSX_Url(self::URL_CALLBACK), 'POST', $header, $body);
		$response = $this->http->request($request);

		$this->assertEquals(200, $response->getCode(), $response->getBody());
		$this->assertEquals('INSERT RSS Heathcliff', $response->getBody());
	}
}


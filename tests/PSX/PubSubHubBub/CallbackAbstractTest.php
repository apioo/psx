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

namespace PSX\Oauth\Provider;

use PSX\Controller\ControllerTestCase;
use PSX\Http;
use PSX\Http\Handler\Callback;
use PSX\Http\GetRequest;
use PSX\Http\PostRequest;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Url;

/**
 * CallbackAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CallbackAbstractTest extends ControllerTestCase
{
	public function testCallbackAtom()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$atom = <<<ATOM
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title type="text">dive into mark</title>
	<subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
	<updated>2005-07-31T12:29:29Z</updated>
	<id>tag:example.org,2003:3</id>
	<link rel="alternate" type="text/html" hreflang="en" href="http://example.org/"/>
	<link rel="self" type="application/atom+xml" href="http://example.org/feed.atom"/>
	<rights>Copyright (c) 2003, Mark Pilgrim</rights>
	<generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
	<entry>
		<title>Atom draft-07 snapshot</title>
		<id>tag:example.org,2003:3.2397</id>
		<published>2003-12-13T08:29:29-04:00</published>
		<content>foobar</content>
	</entry>
</feed>
ATOM;

		$request  = new PostRequest(new Url('http://127.0.0.1/callback'), array('Content-Type' => 'application/atom+xml'), $atom);
		$response = $http->request($request);

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testCallbackAtomEntry()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$atom = <<<ATOM
<?xml version="1.0" encoding="UTF-8"?>
<entry>
	<title>Atom draft-07 snapshot</title>
	<id>tag:example.org,2003:3.2397</id>
	<published>2003-12-13T08:29:29-04:00</published>
	<content>foobar</content>
</entry>
ATOM;

		$request  = new PostRequest(new Url('http://127.0.0.1/callback'), array('Content-Type' => 'application/atom+xml'), $atom);
		$response = $http->request($request);

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testCallbackRss()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$rss = <<<ATOM
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>Liftoff News</title>
		<link>http://liftoff.msfc.nasa.gov/</link>
		<description>Liftoff to Space Exploration.</description>
		<language>en-us</language>
		<pubDate>Tue, 10 Jun 2003 04:00:00 GMT</pubDate>
		<lastBuildDate>Tue, 10 Jun 2003 09:41:01 GMT</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>Weblog Editor 2.0</generator>
		<managingEditor>editor@example.com</managingEditor>
		<webMaster>webmaster@example.com</webMaster>
		<item>
			<title>Star City</title>
			<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
			<pubDate>Tue, 03 Jun 2003 09:39:21 GMT</pubDate>
			<description>foobar</description>
		</item>
	</channel>
</rss>
ATOM;

		$request  = new PostRequest(new Url('http://127.0.0.1/callback'), array('Content-Type' => 'application/rss+xml'), $rss);
		$response = $http->request($request);

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testCallbackRssItem()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$rss = <<<ATOM
<?xml version="1.0" encoding="UTF-8"?>
<item>
	<title>Star City</title>
	<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
	<pubDate>Tue, 03 Jun 2003 09:39:21 GMT</pubDate>
	<description>foobar</description>
</item>
ATOM;

		$request  = new PostRequest(new Url('http://127.0.0.1/callback'), array('Content-Type' => 'application/rss+xml'), $rss);
		$response = $http->request($request);

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testVerify()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$request  = new GetRequest(new Url('http://127.0.0.1/callback?hub.mode=subscribe&hub.topic=http%3A%2F%2F127.0.0.1%2Ftopic&hub.challenge=foobar'));
		$response = $http->request($request);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('foobar', (string) $response->getBody());
	}

	protected function getPaths()
	{
		return array(
			'/callback' => 'PSX\PubSubHubBub\TestCallbackAbstract',
		);
	}
}

<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Transformer;

use PSX\Http\MediaType;

/**
 * RssTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RssTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$body = <<<INPUT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>Liftoff News</title>
		<link>http://liftoff.msfc.nasa.gov/</link>
		<description>Liftoff to Space Exploration.</description>
		<language>en-us</language>
		<pubDate>Tue, 10 Jun 2003 04:00:00 +0000</pubDate>
		<lastBuildDate>Tue, 10 Jun 2003 09:41:01 +0000</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>Weblog Editor 2.0</generator>
		<managingEditor>editor@example.com</managingEditor>
		<webMaster>webmaster@example.com</webMaster>
		<category>News</category>
		<item>
			<title>Star City</title>
			<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
			<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
			<pubDate>Tue, 03 Jun 2003 09:39:21 +0000</pubDate>
			<category domain="http://google.com">News</category>
			<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
			<enclosure url="http://www.scripting.com/mp3s/weatherReportSuite.mp3" length="12216320" type="audio/mpeg" />
			<source url="http://www.tomalak.org/links2.xml">Tomalak's Realm</source>
		</item>
	</channel>
</rss>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Rss();

		$expect = new \stdClass();
		$expect->type = 'rss';
		$expect->title = 'Liftoff News';
		$expect->link = 'http://liftoff.msfc.nasa.gov/';
		$expect->description = 'Liftoff to Space Exploration.';
		$expect->language = 'en-us';
		$expect->pubDate = 'Tue, 10 Jun 2003 04:00:00 +0000';
		$expect->lastBuildDate = 'Tue, 10 Jun 2003 09:41:01 +0000';
		$expect->docs = 'http://blogs.law.harvard.edu/tech/rss';
		$expect->generator = 'Weblog Editor 2.0';
		$expect->managingEditor = 'editor@example.com';
		$expect->webMaster = 'webmaster@example.com';
		$expect->category = new \stdClass();
		$expect->category->text = 'News';
		$expect->item = [];
		$expect->item[0] = new \stdClass();
		$expect->item[0]->type = 'item';
		$expect->item[0]->title = 'Star City';
		$expect->item[0]->link = 'http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp';
		$expect->item[0]->description = 'How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.';
		$expect->item[0]->pubDate = 'Tue, 03 Jun 2003 09:39:21 +0000';
		$expect->item[0]->guid = 'http://liftoff.msfc.nasa.gov/2003/06/03.html#item573';
		$expect->item[0]->category = new \stdClass();
		$expect->item[0]->category->text = 'News';
		$expect->item[0]->category->domain = 'http://google.com';
		$expect->item[0]->enclosure = new \stdClass();
		$expect->item[0]->enclosure->url = 'http://www.scripting.com/mp3s/weatherReportSuite.mp3';
		$expect->item[0]->enclosure->length = '12216320';
		$expect->item[0]->enclosure->type = 'audio/mpeg';
		$expect->item[0]->source = new \stdClass();
		$expect->item[0]->source->url = 'http://www.tomalak.org/links2.xml';
		$expect->item[0]->source->text = 'Tomalak\'s Realm';

		$data = $transformer->transform($dom);

		$this->assertInstanceOf('stdClass', $data);
		$this->assertEquals($expect, $data);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNoRssElement()
	{
		$body = <<<INPUT
<?xml version="1.0" encoding="UTF-8"?>
<foo />
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Rss();
		$transformer->transform($dom);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testNoChannelElement()
	{
		$body = <<<INPUT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
</rss>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Rss();
		$transformer->transform($dom);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidData()
	{
		$transformer = new Rss();
		$transformer->transform(array());
	}

	public function testAccept()
	{
		$transformer = new Rss();

		$this->assertTrue($transformer->accept(new MediaType('application/rss+xml')));
	}

	public function testAcceptInvalid()
	{
		$transformer = new Rss();

		$this->assertFalse($transformer->accept(new MediaType('text/plain')));
	}
}

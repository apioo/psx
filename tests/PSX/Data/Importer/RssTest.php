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

namespace PSX\Data\Importer;

use PSX\Data\Transformer;
use PSX\Http\Message;
use PSX\Rss;
use PSX\Test\Environment;

/**
 * RssTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RssTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        // by default the rss transformer gets not added. We must set a higher
        // priority because else the XmlArray transformer would be used
        Environment::getService('transformer_manager')->addTransformer(new Transformer\Rss(), 24);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Environment::getContainer()->set('transformer_manager', null);
    }

    public function testRss()
    {
        $body = <<<XML
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
			<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
			<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russias &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
			<pubDate>Tue, 03 Jun 2003 09:39:21 GMT</pubDate>
			<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
		</item>
	</channel>
</rss>
XML;

        $request = new Message(array('Content-Type' => 'application/rss+xml'), $body);
        $rss     = Environment::getService('importer')->import(new Rss(), $request);

        $this->assertInstanceOf('PSX\Data\RecordInterface', $rss);
        $this->assertEquals('Liftoff News', $rss->getTitle());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/', $rss->getLink());
        $this->assertEquals('Liftoff to Space Exploration.', $rss->getDescription());
        $this->assertEquals('en-us', $rss->getLanguage());
        $this->assertEquals('2003-06-10', $rss->getPubDate()->format('Y-m-d'));
        $this->assertEquals('2003-06-10', $rss->getLastBuildDate()->format('Y-m-d'));
        $this->assertEquals('http://blogs.law.harvard.edu/tech/rss', $rss->getDocs());
        $this->assertEquals('Weblog Editor 2.0', $rss->getGenerator());
        $this->assertEquals('editor@example.com', $rss->getManagingEditor());
        $this->assertEquals('webmaster@example.com', $rss->getWebMaster());

        $item = $rss->current();

        $this->assertEquals('Star City', $item->getTitle());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->getLink());
        $this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russias <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->getDescription());
        $this->assertEquals('2003-06-03', $item->getPubDate()->format('Y-m-d'));
        $this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->getGuid());
    }

    public function testItem()
    {
        $body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<item>
	<title>Star City</title>
	<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
	<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
	<pubDate>Tue, 03 Jun 2003 09:39:21 GMT</pubDate>
	<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
</item>
XML;

        $request = new Message(array('Content-Type' => 'application/rss+xml'), $body);
        $rss     = Environment::getService('importer')->import(new Rss(), $request);
        $item    = $rss->current();

        $this->assertInstanceOf('PSX\Data\RecordInterface', $item);
        $this->assertEquals('Star City', $item->getTitle());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->getLink());
        $this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->getDescription());
        $this->assertEquals(new \DateTime('Tue, 03 Jun 2003 09:39:21 GMT'), $item->getPubDate());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->getGuid());
    }
}

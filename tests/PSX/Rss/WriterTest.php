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

namespace PSX\Rss;

use DateTime;

/**
 * WriterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriter()
    {
        $writer = new Writer('Liftoff News', 'http://liftoff.msfc.nasa.gov/', 'Liftoff to Space Exploration.');
        $writer->setLanguage('en-us');
        $writer->setPubDate(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
        $writer->setLastBuildDate(new DateTime('Tue, 10 Jun 2003 09:41:01 GMT'));
        $writer->setDocs('http://blogs.law.harvard.edu/tech/rss');
        $writer->setGenerator('Weblog Editor 2.0');
        $writer->setManagingEditor('editor@example.com');
        $writer->setWebMaster('webmaster@example.com');

        $item = $writer->createItem();
        $item->setTitle('Star City');
        $item->setLink('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
        $item->setDescription('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.');
        $item->setPubDate(new DateTime('Tue, 03 Jun 2003 09:39:21 GMT'));
        $item->setGuid('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573');
        $item->close();

        $actual   = $writer->toString();
        $expected = <<<XML
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
		<item>
			<title>Star City</title>
			<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
			<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
			<pubDate>Tue, 03 Jun 2003 09:39:21 +0000</pubDate>
			<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
		</item>
	</channel>
</rss>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }
}

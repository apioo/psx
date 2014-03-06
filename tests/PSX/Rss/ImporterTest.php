<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Rss;

use PSX\Rss;
use PSX\Data\Reader;
use PSX\Http;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;
use PSX\Http\Message;
use PSX\Url;

/**
 * ImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ImporterTest extends \PHPUnit_Framework_TestCase
{
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

		$reader   = new Reader\Dom();
		$rss      = new Rss();
		$importer = new Importer();
		$importer->import($rss, $reader->read(new Message(array(), $body)));

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
}


<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Writer;

use DateTime;
use PSX\Data\Record;
use PSX\Data\WriterTestCase;
use PSX\Rss as RssRecord;
use PSX\Rss\Item;

/**
 * RssTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RssTest extends \PHPUnit_Framework_TestCase
{
	public function testWriteFeed()
	{
		$writer = new Rss();
		$actual = $writer->write($this->getRssRecord());

		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
 <channel>
  <title>Liftoff News</title>
  <link>http://liftoff.msfc.nasa.gov/</link>
  <description>Liftoff to Space Exploration.</description>
  <language>en-us</language>
  <managingEditor>editor@example.com</managingEditor>
  <webMaster>webmaster@example.com</webMaster>
  <pubDate>Tue, 10 Jun 2003 04:00:00 +0000</pubDate>
  <lastBuildDate>Tue, 10 Jun 2003 09:41:01 +0000</lastBuildDate>
  <generator>Weblog Editor 2.0</generator>
  <docs>http://blogs.law.harvard.edu/tech/rss</docs>
  <item>
   <title>Star City</title>
   <link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
   <description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href=&quot;http://howe.iki.rssi.ru/GCTC/gctc_e.htm&quot;&gt;Star City&lt;/a&gt;.</description>
   <guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
   <pubDate>Tue, 03 Jun 2003 09:39:21 +0000</pubDate>
  </item>
 </channel>
</rss>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function testWriteItem()
	{
		$writer = new Rss();
		$actual = $writer->write($this->getItemRecord());

		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<item>
 <title>Star City</title>
 <link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
 <description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href=&quot;http://howe.iki.rssi.ru/GCTC/gctc_e.htm&quot;&gt;Star City&lt;/a&gt;.</description>
 <guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
 <pubDate>Tue, 03 Jun 2003 09:39:21 +0000</pubDate>
</item>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function getRssRecord()
	{
		$rss = new RssRecord();
		$rss->setTitle('Liftoff News');
		$rss->setLink('http://liftoff.msfc.nasa.gov/');
		$rss->setDescription('Liftoff to Space Exploration.');
		$rss->setLanguage('en-us');
		$rss->setPubDate(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
		$rss->setLastBuildDate(new DateTime('Tue, 10 Jun 2003 09:41:01 GMT'));
		$rss->setDocs('http://blogs.law.harvard.edu/tech/rss');
		$rss->setGenerator('Weblog Editor 2.0');
		$rss->setManagingEditor('editor@example.com');
		$rss->setWebMaster('webmaster@example.com');
		$rss->add($this->getItemRecord());

		return $rss;
	}

	public function getItemRecord()
	{
		$item = new Item();
		$item->setTitle('Star City');
		$item->setLink('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
		$item->setDescription('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.');
		$item->setPubDate(new DateTime('Tue, 03 Jun 2003 09:39:21 GMT'));
		$item->setGuid('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573');

		return $item;
	}

	public function testIsContentTypeSupported()
	{
		$writer = new Rss();

		$this->assertTrue($writer->isContentTypeSupported('application/rss+xml'));
		$this->assertFalse($writer->isContentTypeSupported('text/html'));
	}

	public function testGetContentType()
	{
		$writer = new Rss();

		$this->assertEquals('application/rss+xml', $writer->getContentType());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidData()
	{
		$writer = new Rss();
		$writer->write(new Record());
	}
}
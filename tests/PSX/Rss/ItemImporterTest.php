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

namespace PSX\Rss;

use DateTime;
use PSX\Data\Reader;
use PSX\Http;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;
use PSX\Http\Message;

/**
 * EntryImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntryImporterTest extends \PHPUnit_Framework_TestCase
{
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

		$reader   = new Reader\Dom();
		$item     = new Item();
		$importer = new ItemImporter();
		$importer->import($item, $reader->read(new Message(array(), $body)));

		$this->assertEquals('Star City', $item->getTitle());
		$this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->getLink());
		$this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->getDescription());
		$this->assertEquals(new DateTime('Tue, 03 Jun 2003 09:39:21 GMT'), $item->getPubDate());
		$this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->getGuid());
	}
}


<?php
/*
 *  $Id: ItemTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Rss;

use PSX\Data\Reader;
use PSX\DateTime;
use PSX\Http\Message;

/**
 * PSX_Rss_ItemTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testNormalItem()
	{
		$body = <<<XML
<item>
	<title>Star City</title>
	<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
	<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
	<pubDate>Tue, 03 Jun 2003 09:39:21 GMT</pubDate>
	<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
</item>
XML;

		$message = new Message(array(), $body);
		$reader  = new Reader\Dom();

		$item = new Item();
		$item->import($reader->read($message));

		$this->assertEquals('Star City', $item->title);
		$this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->link);
		$this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->description);
		$this->assertEquals(new DateTime('Tue, 03 Jun 2003 09:39:21 GMT'), $item->pubdate);
		$this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->guid);
	}
}
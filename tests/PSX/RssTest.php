<?php
/*
 *  $Id: RssTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_RssTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_RssTest extends PHPUnit_Framework_TestCase
{
	const URL = 'http://test.phpsx.org/index.php/rss/feed';

	private $http;

	protected function setUp()
	{
		$this->http = new PSX_Http(new PSX_Http_Handler_Curl());
	}

	protected function tearDown()
	{
	}

	public function testRss()
	{
		$url = new PSX_Url(self::URL);

		$request  = new PSX_Http_GetRequest($url);

		$response = $this->http->request($request);


		$reader = new PSX_Data_Reader_Dom();

		$rss = new PSX_Rss();

		$rss->import($reader->read($response));


		$this->assertEquals('Liftoff News', $rss->title);
		$this->assertEquals('http://liftoff.msfc.nasa.gov/', $rss->link);
		$this->assertEquals('Liftoff to Space Exploration.', $rss->description);
		$this->assertEquals('en-us', $rss->language);
		$this->assertEquals('2003-06-10', $rss->pubdate->format('Y-m-d'));
		$this->assertEquals('2003-06-10', $rss->lastbuilddate->format('Y-m-d'));
		$this->assertEquals('http://blogs.law.harvard.edu/tech/rss', $rss->docs);
		$this->assertEquals('Weblog Editor 2.0', $rss->generator);
		$this->assertEquals('editor@example.com', $rss->managingeditor);
		$this->assertEquals('webmaster@example.com', $rss->webmaster);

		$item = $rss->current();

		$this->assertEquals('Star City', $item->title);
		$this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->link);
		$this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russias <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->description);
		$this->assertEquals('2003-06-03', $item->pubdate->format('Y-m-d'));
		$this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->guid);
	}
}



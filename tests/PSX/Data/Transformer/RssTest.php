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

namespace PSX\Data\Transformer;

/**
 * RssTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		$expect = array(
			'type' => 'rss',
			'title' => 'Liftoff News',
			'link' => 'http://liftoff.msfc.nasa.gov/',
			'description' => 'Liftoff to Space Exploration.',
			'language' => 'en-us',
			'pubDate' => 'Tue, 10 Jun 2003 04:00:00 +0000',
			'lastBuildDate' => 'Tue, 10 Jun 2003 09:41:01 +0000',
			'docs' => 'http://blogs.law.harvard.edu/tech/rss',
			'generator' => 'Weblog Editor 2.0',
			'managingEditor' => 'editor@example.com',
			'webMaster' => 'webmaster@example.com',
			'category' => array(
				'text' => 'News',
				'domain' => null,
			),
			'item' => array(
				array(
					'type' => 'item',
					'title' => 'Star City',
					'link' => 'http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp',
					'description' => 'How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.',
					'pubDate' => 'Tue, 03 Jun 2003 09:39:21 +0000',
					'guid' => 'http://liftoff.msfc.nasa.gov/2003/06/03.html#item573',
					'category' => array(
						'text' => 'News',
						'domain' => 'http://google.com',
					),
					'enclosure' => array(
						'url' => 'http://www.scripting.com/mp3s/weatherReportSuite.mp3',
						'length' => '12216320',
						'type' => 'audio/mpeg',
					),
					'source' => array(
						'url' => 'http://www.tomalak.org/links2.xml',
						'text' => 'Tomalak\'s Realm',
					),
				)
			),
		);

		$data = $transformer->transform($dom);

		$this->assertTrue(is_array($data));
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
}

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

namespace PSX\Rss;

use DateTime;
use PSX\Data\Writer;
use PSX\Data\SerializeTestAbstract;
use PSX\Rss;

/**
 * SerializeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SerializeTest extends SerializeTestAbstract
{
	public function testSerialize()
	{
		$item = new Item();
		$item->setTitle('Star City');
		$item->setLink('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
		$item->setDescription('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.');
		$item->setAuthor('foobar');
		$item->addCategory(new Category('Newspapers'));
		$item->setComments('http://localhost.com#comments');
		$item->setEnclosure(new Enclosure('http://www.scripting.com/mp3s/weatherReportSuite.mp3', 12216320, 'audio/mpeg'));
		$item->setGuid('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573');
		$item->setPubDate(new DateTime('Tue, 03 Jun 2003 09:39:21 GMT'));
		$item->setSource('Tomalaks Realm');

		$rss = new Rss();
		$rss->setTitle('Liftoff News');
		$rss->setLink('http://liftoff.msfc.nasa.gov/');
		$rss->setDescription('Liftoff to Space Exploration.');
		$rss->setLanguage('en-us');
		$rss->setCopyright('2014 foobar');
		$rss->setManagingEditor('editor@example.com');
		$rss->setWebMaster('webmaster@example.com');
		$rss->setGenerator('Weblog Editor 2.0');
		$rss->setDocs('http://blogs.law.harvard.edu/tech/rss');
		$rss->setTtl(60);
		$rss->setImage('http://localhost.com/image.png');
		$rss->setRating('en');
		$rss->setSkipHours(20);
		$rss->setSkipDays('Tuesday');
		$rss->addCategory(new Category('Newspapers'));
		$rss->setPubDate(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
		$rss->setLastBuildDate(new DateTime('Tue, 10 Jun 2003 09:41:01 GMT'));
		$rss->setCloud(new Cloud('rpc.sys.com', 80, '/RPC2', 'pingMe', 'soap'));
		$rss->add($item);

		$content = <<<JSON
{
  "title": "Liftoff News",
  "link": "http://liftoff.msfc.nasa.gov/",
  "description": "Liftoff to Space Exploration.",
  "language": "en-us",
  "copyright": "2014 foobar",
  "managingEditor": "editor@example.com",
  "webMaster": "webmaster@example.com",
  "generator": "Weblog Editor 2.0",
  "docs": "http://blogs.law.harvard.edu/tech/rss",
  "ttl": 60,
  "image": "http://localhost.com/image.png",
  "rating": "en",
  "skipHours": 20,
  "skipDays": "Tuesday",
  "category": [{
  	"text": "Newspapers"
  }],
  "pubDate": "2003-06-10T04:00:00+00:00",
  "lastBuildDate": "2003-06-10T09:41:01+00:00",
  "cloud": {
    "domain": "rpc.sys.com",
    "port": 80,
    "path": "/RPC2",
    "registerProcedure": "pingMe",
    "protocol": "soap"
  },
  "item": [{
    "title": "Star City",
    "link": "http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp",
    "description": "How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's <a href=\"http://howe.iki.rssi.ru/GCTC/gctc_e.htm\">Star City</a>.",
    "author": "foobar",
    "category": [{
    	"text": "Newspapers"
    }],
    "guid": "http://liftoff.msfc.nasa.gov/2003/06/03.html#item573",
    "pubDate": "2003-06-03T09:39:21+00:00",
    "comments": "http://localhost.com#comments",
    "enclosure": {
      "url": "http://www.scripting.com/mp3s/weatherReportSuite.mp3",
      "length": 12216320,
      "type": "audio/mpeg"
    },
    "source": "Tomalaks Realm"
  }]
} 
JSON;

		$this->assertRecordEqualsContent($rss, $content);
	}
}

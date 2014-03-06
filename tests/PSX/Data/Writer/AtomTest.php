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

namespace PSX\Data\Writer;

use DateTime;
use PSX\Atom as AtomRecord;
use PSX\Atom\Category;
use PSX\Atom\Entry;
use PSX\Atom\Generator;
use PSX\Atom\Link;
use PSX\Atom\Person;
use PSX\Atom\Text;
use PSX\Data\WriterTestCase;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AtomTest extends \PHPUnit_Framework_TestCase
{
	public function testWriteFeed()
	{
		$writer = new Atom();
		$actual = $writer->write($this->getAtomRecord());

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
 <title>dive into mark</title>
 <id>tag:example.org,2003:3</id>
 <updated>2005-07-31T12:29:29+00:00</updated>
 <subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
 <link href="http://example.org/" rel="alternate" type="text/html" hreflang="en"/>
 <link href="http://example.org/feed.atom" rel="self" type="application/atom+xml"/>
 <rights>Copyright (c) 2003, Mark Pilgrim</rights>
 <generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
 <entry>
  <id>tag:example.org,2003:3.2397</id>
  <title>Atom draft-07 snapshot</title>
  <updated>2005-07-31T12:29:29+00:00</updated>
  <published>2003-12-13T08:29:29-04:00</published>
  <link href="http://example.org/2005/04/02/atom" rel="alternate" type="text/html"/>
  <link href="http://example.org/audio/ph34r_my_podcast.mp3" rel="enclosure" type="audio/mpeg" length="1337"/>
  <author>
   <name>Mark Pilgrim</name>
   <uri>http://example.org/</uri>
   <email>f8dy@example.com</email>
  </author>
  <contributor>
   <name>Sam Ruby</name>
  </contributor>
  <contributor>
   <name>Joe Gregorio</name>
  </contributor>
  <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div></content>
  <source>
   <title>dive into mark</title>
   <id>tag:example.org,2003:3</id>
   <updated>2005-07-31T12:29:29+00:00</updated>
   <subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
   <link href="http://example.org/" rel="alternate" type="text/html" hreflang="en"/>
   <link href="http://example.org/feed.atom" rel="self" type="application/atom+xml"/>
   <rights>Copyright (c) 2003, Mark Pilgrim</rights>
   <generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
  </source>
 </entry>
</feed>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function testWriteEntry()
	{
		$writer = new Atom();
		$actual = $writer->write($this->getEntryRecord());

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <id>tag:example.org,2003:3.2397</id>
 <title>Atom draft-07 snapshot</title>
 <updated>2005-07-31T12:29:29+00:00</updated>
 <published>2003-12-13T08:29:29-04:00</published>
 <link href="http://example.org/2005/04/02/atom" rel="alternate" type="text/html"/>
 <link href="http://example.org/audio/ph34r_my_podcast.mp3" rel="enclosure" type="audio/mpeg" length="1337"/>
 <author>
  <name>Mark Pilgrim</name>
  <uri>http://example.org/</uri>
  <email>f8dy@example.com</email>
 </author>
 <contributor>
  <name>Sam Ruby</name>
 </contributor>
 <contributor>
  <name>Joe Gregorio</name>
 </contributor>
 <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div></content>
 <source>
  <title>dive into mark</title>
  <id>tag:example.org,2003:3</id>
  <updated>2005-07-31T12:29:29+00:00</updated>
  <subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
  <link href="http://example.org/" rel="alternate" type="text/html" hreflang="en"/>
  <link href="http://example.org/feed.atom" rel="self" type="application/atom+xml"/>
  <rights>Copyright (c) 2003, Mark Pilgrim</rights>
  <generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
 </source>
</entry>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	protected function getAtomRecord()
	{
		$atom = new AtomRecord();
		$atom->setTitle('dive into mark');
		$atom->setSubTitle(new Text('A <em>lot</em> of effort went into making this effortless', 'html'));
		$atom->setUpdated(new DateTime('2005-07-31T12:29:29Z'));
		$atom->setId('tag:example.org,2003:3');
		$atom->addLink(new Link('http://example.org/', 'alternate', 'text/html', 'en'));
		$atom->addLink(new Link('http://example.org/feed.atom', 'self', 'application/atom+xml'));
		$atom->setRights('Copyright (c) 2003, Mark Pilgrim');
		$atom->setGenerator(new Generator('Example Toolkit', 'http://www.example.com/', '1.0'));
		$atom->add($this->getEntryRecord());

		return $atom;
	}

	protected function getEntryRecord()
	{
		$entry = new Entry();
		$entry->setTitle('Atom draft-07 snapshot');
		$entry->addLink(new Link('http://example.org/2005/04/02/atom', 'alternate', 'text/html'));
		$entry->addLink(new Link('http://example.org/audio/ph34r_my_podcast.mp3', 'enclosure', 'audio/mpeg', null, null, 1337));
		$entry->setId('tag:example.org,2003:3.2397');
		$entry->setUpdated(new DateTime('2005-07-31T12:29:29'));
		$entry->setPublished(new DateTime('2003-12-13T08:29:29-04:00'));
		$entry->addAuthor(new Person('Mark Pilgrim', 'http://example.org/', 'f8dy@example.com'));
		$entry->addContributor(new Person('Sam Ruby'));
		$entry->addContributor(new Person('Joe Gregorio'));
		$entry->setContent(new Text('<div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div>', 'xhtml'));
		
		$atom = new AtomRecord();
		$atom->setTitle('dive into mark');
		$atom->setSubTitle(new Text('A <em>lot</em> of effort went into making this effortless', 'html'));
		$atom->setUpdated(new DateTime('2005-07-31T12:29:29Z'));
		$atom->setId('tag:example.org,2003:3');
		$atom->addLink(new Link('http://example.org/', 'alternate', 'text/html', 'en'));
		$atom->addLink(new Link('http://example.org/feed.atom', 'self', 'application/atom+xml'));
		$atom->setRights('Copyright (c) 2003, Mark Pilgrim');
		$atom->setGenerator(new Generator('Example Toolkit', 'http://www.example.com/', '1.0'));

		$entry->setSource($atom);

		return $entry;
	}
}

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

namespace PSX\Data\Writer;

use DateTime;
use PSX\Atom as AtomRecord;
use PSX\Atom\Category;
use PSX\Atom\Entry;
use PSX\Atom\Generator;
use PSX\Atom\Link;
use PSX\Atom\Person;
use PSX\Atom\Text;
use PSX\Data\Record;
use PSX\Data\WriterTestCase;
use PSX\Data\WriterTestRecord;
use PSX\Http\MediaType;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
 <author>
  <name>Mark Pilgrim</name>
  <uri>http://example.org/</uri>
  <email>f8dy@example.com</email>
 </author>
 <category term="news"/>
 <contributor>
  <name>Sam Ruby</name>
 </contributor>
 <icon>http://localhost.com/icon.png</icon>
 <logo>http://localhost.com/logo.png</logo>
 <entry>
  <id>tag:example.org,2003:3.2397</id>
  <title>Atom draft-07 snapshot</title>
  <updated>2005-07-31T12:29:29+00:00</updated>
  <published>2003-12-13T08:29:29-04:00</published>
  <link href="http://example.org/2005/04/02/atom" rel="alternate" type="text/html"/>
  <link href="http://example.org/audio/ph34r_my_podcast.mp3" rel="enclosure" type="audio/mpeg" length="1337"/>
  <rights>Copyright (c) 2003, Mark Pilgrim</rights>
  <author>
   <name>Mark Pilgrim</name>
   <uri>http://example.org/</uri>
   <email>f8dy@example.com</email>
  </author>
  <category term="news"/>
  <contributor>
   <name>Sam Ruby</name>
  </contributor>
  <contributor>
   <name>Joe Gregorio</name>
  </contributor>
  <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div></content>
  <summary type="text">foobar</summary>
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
 <rights>Copyright (c) 2003, Mark Pilgrim</rights>
 <author>
  <name>Mark Pilgrim</name>
  <uri>http://example.org/</uri>
  <email>f8dy@example.com</email>
 </author>
 <category term="news"/>
 <contributor>
  <name>Sam Ruby</name>
 </contributor>
 <contributor>
  <name>Joe Gregorio</name>
 </contributor>
 <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div></content>
 <summary type="text">foobar</summary>
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

	public function testWriteEmpty()
	{
		$writer = new Atom();
		$actual = $writer->write(new Record('record', []));

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
  <content type="application/xml">
    <record />
  </content>
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
		$atom->addAuthor(new Person('Mark Pilgrim', 'http://example.org/', 'f8dy@example.com'));
		$atom->addContributor(new Person('Sam Ruby'));
		$atom->addCategory(new Category('news'));
		$atom->setIcon('http://localhost.com/icon.png');
		$atom->setLogo('http://localhost.com/logo.png');
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
		$entry->addCategory(new Category('news'));
		$entry->setContent(new Text('<div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div>', 'xhtml'));
		$entry->setSummary(new Text('foobar', 'text'));
		$entry->setRights('Copyright (c) 2003, Mark Pilgrim');

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

	public function testIsContentTypeSupported()
	{
		$writer = new Atom();

		$this->assertTrue($writer->isContentTypeSupported(new MediaType('application/atom+xml')));
		$this->assertFalse($writer->isContentTypeSupported(new MediaType('application/xml')));
	}

	public function testGetContentType()
	{
		$writer = new Atom();

		$this->assertEquals('application/atom+xml', $writer->getContentType());
	}

	public function testWriteRecord()
	{
		$record = new WriterTestRecord();
		$record->setId(1);
		$record->setAuthor('foo');
		$record->setTitle('bar');
		$record->setContent('foobar');

		$writer = new Atom();
		$actual = $writer->write($record);

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
  <content type="application/xml">
    <record>
      <id>1</id>
      <author>foo</author>
      <title>bar</title>
      <content>foobar</content>
    </record>
  </content>
</entry>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}
}

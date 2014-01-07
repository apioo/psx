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

namespace PSX\Atom;

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
	const URL = 'http://test.phpsx.org/atom/feed';

	private $http;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Atom/atom_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Atom/atom_http_fixture.xml');

		$this->http = new Http($mock);
	}

	protected function tearDown()
	{
		unset($this->http);
	}

	public function testEntry()
	{
		$body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry>
	<title>Atom-Powered Robots Run Amok</title>
	<link href="http://example.org/2003/12/13/atom03"/>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02Z</updated>
	<summary>Some text.</summary>
</entry>
XML;

		$reader   = new Reader\Dom();
		$entry    = new Entry();
		$importer = new EntryImporter();
		$importer->import($entry, $reader->read(new Message(array(), $body)));

		$link = current($entry->getLink());

		$this->assertEquals('Atom-Powered Robots Run Amok', $entry->getTitle());
		$this->assertEquals('http://example.org/2003/12/13/atom03', $link->getHref());
		$this->assertEquals('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a', $entry->getId());
		$this->assertEquals(new DateTime('2003-12-13T18:30:02Z'), $entry->getUpdated());
		$this->assertEquals('Some text.', $entry->getSummary());
	}

	public function testRemoteXmlContent()
	{
		$url  = ImporterTest::URL;
		$body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry>
	<title>Atom-Powered Robots Run Amok</title>
	<link href="http://example.org/2003/12/13/atom03"/>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02Z</updated>
	<summary>Some text.</summary>
	<content type="application/xml" src="{$url}"></content>
</entry>
XML;

		$reader   = new Reader\Dom();
		$entry    = new Entry();
		$importer = new EntryImporter();
		$importer->setFetchRemoteContent($this->http);
		$importer->import($entry, $reader->read(new Message(array(), $body)));

		$expect = <<<XML
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:default="http://www.w3.org/1999/xhtml">
	<title type="text">dive into mark</title>
	<subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
	<updated>2005-07-31T12:29:29Z</updated>
	<id>tag:example.org,2003:3</id>
	<link rel="alternate" type="text/html" hreflang="en" href="http://example.org/"/>
	<link rel="self" type="application/atom+xml" href="http://example.org/feed.atom"/>
	<rights>Copyright (c) 2003, Mark Pilgrim</rights>
	<generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
	<entry>
		<title>Atom draft-07 snapshot</title>
		<link rel="alternate" type="text/html" href="http://example.org/2005/04/02/atom"/>
		<link rel="enclosure" type="audio/mpeg" length="1337" href="http://example.org/audio/ph34r_my_podcast.mp3"/>
		<id>tag:example.org,2003:3.2397</id>
		<updated>2005-07-31T12:29:29Z</updated>
		<published>2003-12-13T08:29:29-04:00</published>
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
		<content type="xhtml" xml:lang="en" xml:base="http://diveintomark.org/">
			<default:div xmlns="http://www.w3.org/1999/xhtml">
				<default:p><default:i>[Update: The Atom draft is finished.]</default:i></default:p>
			</default:div>
		</content>
	</entry>
</feed>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $entry->getContent());
	}

	public function testRemoteTextContent()
	{
		$body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry>
	<title>Atom-Powered Robots Run Amok</title>
	<link href="http://example.org/2003/12/13/atom03"/>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02Z</updated>
	<summary>Some text.</summary>
	<content type="text/plain" src="http://www.google.de/humans.txt"></content>
</entry>
XML;

		$reader   = new Reader\Dom();
		$entry    = new Entry();
		$importer = new EntryImporter();
		$importer->setFetchRemoteContent($this->http);
		$importer->import($entry, $reader->read(new Message(array(), $body)));

		$expect = <<<XML
Google is built by a large team of engineers, designers, researchers, robots, and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you'd like to help us out, see google.com/jobs.
XML;

		$this->assertEquals($expect, $entry->getContent());
	}
}


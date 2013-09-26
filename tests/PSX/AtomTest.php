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

namespace PSX;

use PSX\Data\Reader;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;
use PSX\Http\Message;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AtomTest extends \PHPUnit_Framework_TestCase
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

	public function testAtom()
	{
		$url      = new Url(self::URL);
		$request  = new GetRequest($url);
		$response = $this->http->request($request);
		$reader   = new Reader\Dom();

		$atom = new Atom();
		$atom->import($reader->read($response));

		$this->assertEquals('dive into mark', $atom->title);
		$this->assertEquals('A <em>lot</em> of effort went into making this effortless', $atom->subtitle);
		$this->assertEquals('2005-07-31', $atom->updated->format('Y-m-d'));
		$this->assertEquals('tag:example.org,2003:3', $atom->id);
		$this->assertEquals(array('href' => 'http://example.org/', 'rel' => 'alternate', 'type' => 'text/html', 'hreflang' => 'en', 'title' => '', 'length' => ''), $atom->link[0]);
		$this->assertEquals(array('href' => 'http://example.org/feed.atom', 'rel' => 'self', 'type' => 'application/atom+xml', 'hreflang' => '', 'title' => '', 'length' => ''), $atom->link[1]);
		$this->assertEquals('Copyright (c) 2003, Mark Pilgrim', $atom->rights);
		$this->assertEquals('Example Toolkit', $atom->generator);

		$entry = $atom->current();

		$this->assertEquals('Atom draft-07 snapshot', $entry->title);
		$this->assertEquals(array('href' => 'http://example.org/2005/04/02/atom', 'rel' => 'alternate', 'type' => 'text/html', 'hreflang' => '', 'title' => '', 'length' => ''), $entry->link[0]);
		$this->assertEquals(array('href' => 'http://example.org/audio/ph34r_my_podcast.mp3', 'rel' => 'enclosure', 'type' => 'audio/mpeg', 'hreflang' => '', 'title' => '', 'length' => '1337'), $entry->link[1]);
		$this->assertEquals('tag:example.org,2003:3.2397', $entry->id);
		$this->assertEquals('2005-07-31', $entry->updated->format('Y-m-d'));
		$this->assertEquals('2003-12-13', $entry->published->format('Y-m-d'));
		$this->assertEquals(array('name' => 'Mark Pilgrim', 'uri' => 'http://example.org/', 'email' => 'f8dy@example.com'), $entry->author[0]);
		$this->assertEquals(array('name' => 'Sam Ruby'), $entry->contributor[0]);
		$this->assertEquals(array('name' => 'Joe Gregorio'), $entry->contributor[1]);
		$this->assertXmlStringEqualsXmlString('<div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div>', $entry->content);
	}

	public function testAtomSource()
	{
		$feed = <<<FEED
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
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
			<div xmlns="http://www.w3.org/1999/xhtml">
				<p><i>[Update: The Atom draft is finished.]</i></p>
			</div>
		</content>
		<source>
			<title type="text">dive into mark</title>
			<subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
			<updated>2005-07-31T12:29:29Z</updated>
			<id>tag:example.org,2003:3</id>
			<link rel="alternate" type="text/html" hreflang="en" href="http://example.org/"/>
			<link rel="self" type="application/atom+xml" href="http://example.org/feed.atom"/>
			<rights>Copyright (c) 2003, Mark Pilgrim</rights>
			<generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
		</source>
	</entry>
</feed>
FEED;

		$reader = new Reader\Dom();
		$atom   = new Atom();
		$atom->import($reader->read(new Message(array(), $feed)));

		$entry = $atom->current();

		$this->assertEquals(true, $entry->source instanceof Atom);
		$this->assertEquals('dive into mark', $entry->source->title);
		$this->assertEquals('A <em>lot</em> of effort went into making this effortless', $entry->source->subtitle);
		$this->assertEquals('2005-07-31', $entry->source->updated->format('Y-m-d'));
		$this->assertEquals('tag:example.org,2003:3', $entry->source->id);
		$this->assertEquals(array('href' => 'http://example.org/', 'rel' => 'alternate', 'type' => 'text/html', 'hreflang' => 'en', 'title' => '', 'length' => ''), $entry->source->link[0]);
		$this->assertEquals(array('href' => 'http://example.org/feed.atom', 'rel' => 'self', 'type' => 'application/atom+xml', 'hreflang' => '', 'title' => '', 'length' => ''), $entry->source->link[1]);
		$this->assertEquals('Copyright (c) 2003, Mark Pilgrim', $entry->source->rights);
		$this->assertEquals('Example Toolkit', $entry->source->generator);
	}
}


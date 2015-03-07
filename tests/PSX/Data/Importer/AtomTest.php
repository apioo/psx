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

namespace PSX\Data\Importer;

use PSX\Atom;
use PSX\Http\Message;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AtomTest extends \PHPUnit_Framework_TestCase
{
	public function testAtom()
	{
		$body = <<<XML
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
	</entry>
</feed>
XML;

		$request = new Message(array('Content-Type' => 'application/atom+xml'), $body);
		$atom    = getContainer()->get('importer')->import(new Atom(), $request);

		$this->assertInstanceOf('PSX\Atom', $atom);
		$this->assertEquals('dive into mark', $atom->getTitle());
		$this->assertEquals('A <em>lot</em> of effort went into making this effortless', $atom->getSubTitle());
		$this->assertEquals('2005-07-31', $atom->getUpdated()->format('Y-m-d'));
		$this->assertEquals('tag:example.org,2003:3', $atom->getId());

		$links = $atom->getLink();
		$this->assertInstanceOf('PSX\Atom\Link', $links[0]);
		$this->assertEquals('http://example.org/', $links[0]->getHref());
		$this->assertEquals('alternate', $links[0]->getRel());
		$this->assertEquals('text/html', $links[0]->getType());
		$this->assertEquals('en', $links[0]->getHrefLang());
		$this->assertEquals('', $links[0]->getTitle());
		$this->assertEquals('', $links[0]->getLength());

		$this->assertInstanceOf('PSX\Atom\Link', $links[1]);
		$this->assertEquals('http://example.org/feed.atom', $links[1]->getHref());
		$this->assertEquals('self', $links[1]->getRel());
		$this->assertEquals('application/atom+xml', $links[1]->getType());
		$this->assertEquals('', $links[1]->getHrefLang());
		$this->assertEquals('', $links[1]->getTitle());
		$this->assertEquals('', $links[1]->getLength());

		$this->assertEquals('Copyright (c) 2003, Mark Pilgrim', $atom->getRights());
		$this->assertEquals('Example Toolkit', $atom->getGenerator());

		$entry = $atom->current();

		$this->assertInstanceOf('PSX\Atom\Entry', $entry);
		$this->assertEquals('Atom draft-07 snapshot', $entry->getTitle());

		$links = $entry->getLink();
		$this->assertInstanceOf('PSX\Atom\Link', $links[0]);
		$this->assertEquals('http://example.org/2005/04/02/atom', $links[0]->getHref());
		$this->assertEquals('alternate', $links[0]->getRel());
		$this->assertEquals('text/html', $links[0]->getType());
		$this->assertEquals('', $links[0]->getHrefLang());
		$this->assertEquals('', $links[0]->getTitle());
		$this->assertEquals('', $links[0]->getLength());

		$this->assertInstanceOf('PSX\Atom\Link', $links[1]);
		$this->assertEquals('http://example.org/audio/ph34r_my_podcast.mp3', $links[1]->getHref());
		$this->assertEquals('enclosure', $links[1]->getRel());
		$this->assertEquals('audio/mpeg', $links[1]->getType());
		$this->assertEquals('', $links[1]->getHrefLang());
		$this->assertEquals('', $links[1]->getTitle());
		$this->assertEquals('1337', $links[1]->getLength());

		$this->assertEquals('tag:example.org,2003:3.2397', $entry->getId());
		$this->assertEquals('2005-07-31', $entry->getUpdated()->format('Y-m-d'));
		$this->assertEquals('2003-12-13', $entry->getPublished()->format('Y-m-d'));

		$authors = $entry->getAuthor();
		$this->assertInstanceOf('PSX\Atom\Person', $authors[0]);
		$this->assertEquals('Mark Pilgrim', $authors[0]->getName());
		$this->assertEquals('http://example.org/', $authors[0]->getUri());
		$this->assertEquals('f8dy@example.com', $authors[0]->getEmail());

		$contributors = $entry->getContributor();
		$this->assertInstanceOf('PSX\Atom\Person', $contributors[0]);
		$this->assertEquals('Sam Ruby', $contributors[0]->getName());
		$this->assertEquals('Joe Gregorio', $contributors[1]->getName());

		$expected = <<<HTML
<div xmlns="http://www.w3.org/1999/xhtml">
	<p><i>[Update: The Atom draft is finished.]</i></p>
</div>
HTML;
		$this->assertXmlStringEqualsXmlString($expected, $entry->getContent());
	}

	public function testAtomSource()
	{
		$body = <<<XML
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
XML;

		$request = new Message(array('Content-Type' => 'application/atom+xml'), $body);
		$atom    = getContainer()->get('importer')->import(new Atom(), $request);

		$entry = $atom->current();

		$this->assertInstanceOf('PSX\Atom\Entry', $entry);
		$this->assertInstanceOf('PSX\Atom', $entry->getSource());
		$this->assertEquals('dive into mark', $entry->getSource()->getTitle());
		$this->assertEquals('A <em>lot</em> of effort went into making this effortless', $entry->getSource()->getSubTitle());
		$this->assertEquals('2005-07-31', $entry->getSource()->getUpdated()->format('Y-m-d'));
		$this->assertEquals('tag:example.org,2003:3', $entry->getSource()->getId());

		$links = $entry->getSource()->getLink();
		$this->assertInstanceOf('PSX\Atom\Link', $links[0]);
		$this->assertEquals('http://example.org/', $links[0]->getHref());
		$this->assertEquals('alternate', $links[0]->getRel());
		$this->assertEquals('text/html', $links[0]->getType());
		$this->assertEquals('en', $links[0]->getHrefLang());
		$this->assertEquals('', $links[0]->getTitle());
		$this->assertEquals('', $links[0]->getLength());

		$this->assertInstanceOf('PSX\Atom\Link', $links[1]);
		$this->assertEquals('http://example.org/feed.atom', $links[1]->getHref());
		$this->assertEquals('self', $links[1]->getRel());
		$this->assertEquals('application/atom+xml', $links[1]->getType());
		$this->assertEquals('', $links[1]->getHrefLang());
		$this->assertEquals('', $links[1]->getTitle());
		$this->assertEquals('', $links[1]->getLength());

		$this->assertEquals('Copyright (c) 2003, Mark Pilgrim', $entry->getSource()->getRights());
		$this->assertEquals('Example Toolkit', $entry->getSource()->getGenerator());
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

		$request = new Message(array('Content-Type' => 'application/atom+xml'), $body);
		$atom    = getContainer()->get('importer')->import(new Atom(), $request);
		$entry   = $atom->current();

		$this->assertInstanceOf('PSX\Atom\Entry', $entry);
		$this->assertEquals('Atom-Powered Robots Run Amok', $entry->getTitle());
		$this->assertEquals('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a', $entry->getId());
		$this->assertEquals(new \DateTime('2003-12-13T18:30:02Z'), $entry->getUpdated());
		$this->assertEquals('Some text.', $entry->getSummary());

		$link = current($entry->getLink());

		$this->assertInstanceOf('PSX\Atom\Link', $link);
		$this->assertEquals('http://example.org/2003/12/13/atom03', $link->getHref());
	}
}


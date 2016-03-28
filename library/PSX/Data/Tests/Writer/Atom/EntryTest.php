<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Tests\Writer\Atom;

use DateTime;
use PSX\Atom\Writer;
use PSX\Data\Record;
use PSX\Data\Writer\Atom\Entry;

/**
 * EntryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EntryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetContentXmlRecord()
    {
        $record = new Record('foo', array(
            'title' => 'bar',
            'bar'   => 'foo',
        ));

        $entry = new Entry();
        $entry->setTitle('Atom-Powered Robots Run Amok');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($record, 'application/xml');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="application/xml">
		<foo>
			<title>bar</title>
			<bar>foo</bar>
		</foo>
	</content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentXmlString()
    {
        $data = '<foo type="bar"><bar /></foo>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered Robots Run Amok');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($data, 'application/xml');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="application/xml">
		<foo type="bar"><bar /></foo>
	</content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentHtmlString()
    {
        $data = '<h1>foobar</h1>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered <b>Robots</b> Run Amok', 'html');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($data, 'html');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title type="html">Atom-Powered &lt;b&gt;Robots&lt;/b&gt; Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="html">&lt;h1&gt;foobar&lt;/h1&gt;</content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentTextString()
    {
        $data = '<h1>foobar</h1>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered Robots Run Amok', 'text');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($data, 'text');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title type="text">Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="text">&lt;h1&gt;foobar&lt;/h1&gt;</content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentXhtmlString()
    {
        $data = '<h1>foobar</h1>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered <b>Robots</b> Run Amok', 'xhtml');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($data, 'xhtml');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title type="xhtml">Atom-Powered <b>Robots</b> Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="xhtml"><h1>foobar</h1></content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentXmlMediaTypeString()
    {
        $data = '<h1>foobar</h1>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered Robots Run Amok');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($data, 'application/foo+xml');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="application/foo+xml"><h1>foobar</h1></content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentUnknownString()
    {
        $data = '<h1>foobar</h1>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered Robots Run Amok');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent($data, 'foo');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content type="foo">&lt;h1&gt;foobar&lt;/h1&gt;</content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testSetContentSource()
    {
        $data = '<h1>foobar</h1>';

        $entry = new Entry();
        $entry->setTitle('Atom-Powered Robots Run Amok');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->setContent(null, 'application/xml', 'http://foo.com/data.xml');
        $entry->close();

        $actual   = $entry->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
	<title>Atom-Powered Robots Run Amok</title>
	<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<content src="http://foo.com/data.xml" type="application/xml"/>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testTextConstructXhtml()
    {
    }
}

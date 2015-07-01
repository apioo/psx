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

use PSX\Data\WriterTestCase;
use PSX\Http\MediaType;

/**
 * XmlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlTest extends WriterTestCase
{
    public function testWrite()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<record>
  <id>1</id>
  <author>foo</author>
  <title>bar</title>
  <content>foobar</content>
  <date>2012-03-11T13:37:21Z</date>
</record>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteResultSet()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getResultSet());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<resultset>
  <totalResults>2</totalResults>
  <startIndex>0</startIndex>
  <itemsPerPage>8</itemsPerPage>
  <entry>
    <id>1</id>
    <author>foo</author>
    <title>bar</title>
    <content>foobar</content>
    <date>2012-03-11T13:37:21Z</date>
  </entry>
  <entry>
    <id>2</id>
    <author>foo</author>
    <title>bar</title>
    <content>foobar</content>
    <date>2012-03-11T13:37:21Z</date>
  </entry>
</resultset>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteComplex()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getComplexRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<activity>
  <verb>post</verb>
  <actor>
    <id>tag:example.org,2011:martin</id>
    <objectType>person</objectType>
    <displayName>Martin Smith</displayName>
    <url>http://example.org/martin</url>
  </actor>
  <object>
    <id>tag:example.org,2011:abc123/xyz</id>
    <url>http://example.org/blog/2011/02/entry</url>
  </object>
  <target>
    <id>tag:example.org,2011:abc123</id>
    <objectType>blog</objectType>
    <displayName>Martin's Blog</displayName>
    <url>http://example.org/blog/</url>
  </target>
  <published>2011-02-10T15:04:55Z</published>
</activity>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getEmptyRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<record />
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Xml();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/xml')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
    }

    public function testGetContentType()
    {
        $writer = new Xml();

        $this->assertEquals('application/xml', $writer->getContentType());
    }
}

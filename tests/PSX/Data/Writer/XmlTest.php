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

use PSX\Data\WriterTestCase;

/**
 * XmlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
  <date>2012-03-11T13:37:21+00:00</date>
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
    <date>2012-03-11T13:37:21+00:00</date>
  </entry>
  <entry>
    <id>2</id>
    <author>foo</author>
    <title>bar</title>
    <content>foobar</content>
    <date>2012-03-11T13:37:21+00:00</date>
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
  <published>2011-02-10T15:04:55+00:00</published>
</activity>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}
}
<?php
/*
 *  $Id: XmlTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Data_Writer_XmlTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_Data_Writer_XmlTest extends PSX_Data_WriterTestCase
{
	public function testWrite()
	{
		ob_start();

		$writer = new PSX_Data_Writer_Xml();
		$writer->write($this->getRecord());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<record>
  <id>1</id>
  <author>foo</author>
  <title>bar</title>
  <content>foobar</content>
  <date>2012-03-11 13:37:21</date>
</record>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function testWriteResultSet()
	{
		ob_start();

		$writer = new PSX_Data_Writer_Xml();
		$writer->write($this->getResultSet());

		$actual = ob_get_contents();

		ob_end_clean();


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
    <date>2012-03-11 13:37:21</date>
  </entry>
  <entry>
    <id>2</id>
    <author>foo</author>
    <title>bar</title>
    <content>foobar</content>
    <date>2012-03-11 13:37:21</date>
  </entry>
</resultset>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function testWriteComplex()
	{
		ob_start();

		$writer = new PSX_Data_Writer_Xml();
		$writer->write($this->getComplexRecord());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<article>
  <objectType>article</objectType>
  <author>
    <accounts>
      <value>
        <domain>foo.com</domain>
        <username>foo</username>
        <userId>1</userId>
      </value>
      <type>home</type>
      <primary>true</primary>
    </accounts>
    <accounts>
      <value>
        <domain>bar.com</domain>
        <username>foo</username>
        <userId>1</userId>
      </value>
      <type>work</type>
      <primary>false</primary>
    </accounts>
    <displayName>foobar</displayName>
    <id>1</id>
  </author>
  <displayName>content</displayName>
  <id>1</id>
</article>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}
}
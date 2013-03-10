<?php
/*
 *  $Id: AtomTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Data\Writer;

use PSX\Data\WriterTestCase;
use PSX\DateTime;

/**
 * PSX_Data_Writer_AtomTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class AtomTest extends WriterTestCase
{
	public function testWrite()
	{
		ob_start();

		$writer = new Atom();
		$writer->write($this->getRecord());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
  <title>bar</title>
  <id>1</id>
  <updated>2012-03-11T13:37:21+00:00</updated>
  <author>
    <name>foo</name>
  </author>
  <content type="html">foobar</content>
</entry>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function testWriteResultSet()
	{
		ob_start();

		$writer = new Atom();
		$writer->setConfig('foo', 'bar', new DateTime('2012-03-11 13:37:21'));
		$writer->setGenerator('amun');
		$writer->write($this->getResultSet());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>foo</title>
  <id>bar</id>
  <updated>2012-03-11T13:37:21+00:00</updated>
  <generator>amun</generator>
  <entry xmlns="http://www.w3.org/2005/Atom">
    <title>bar</title>
    <id>1</id>
    <updated>2012-03-11T13:37:21+00:00</updated>
    <author>
      <name>foo</name>
    </author>
    <content type="html">foobar</content>
  </entry>
  <entry xmlns="http://www.w3.org/2005/Atom">
    <title>bar</title>
    <id>2</id>
    <updated>2012-03-11T13:37:21+00:00</updated>
    <author>
      <name>foo</name>
    </author>
    <content type="html">foobar</content>
  </entry>
</feed>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}
}

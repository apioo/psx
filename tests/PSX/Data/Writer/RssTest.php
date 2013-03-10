<?php
/*
 *  $Id: RssTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_Data_Writer_RssTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class RssTest extends WriterTestCase
{
	public function testWrite()
	{
		ob_start();

		$writer = new Rss();
		$writer->write($this->getRecord());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<item>
  <title>bar</title>
  <guid>1</guid>
  <pubDate>Sun, 11 Mar 2012 13:37:21 +0000</pubDate>
  <author>foo</author>
  <description>foobar</description>
</item>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}

	public function testWriteResultSet()
	{
		ob_start();

		$writer = new Rss();
		$writer->setConfig('foo', '#', 'bar');
		$writer->write($this->getResultSet());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>foo</title>
    <link>#</link>
    <description>bar</description>
    <item>
      <title>bar</title>
      <guid>1</guid>
      <pubDate>Sun, 11 Mar 2012 13:37:21 +0000</pubDate>
      <author>foo</author>
      <description>foobar</description>
    </item>
    <item>
      <title>bar</title>
      <guid>2</guid>
      <pubDate>Sun, 11 Mar 2012 13:37:21 +0000</pubDate>
      <author>foo</author>
      <description>foobar</description>
    </item>
  </channel>
</rss>
TEXT;

		$this->assertXmlStringEqualsXmlString($expect, $actual);
	}
}
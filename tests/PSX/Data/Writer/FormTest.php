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
 * FormTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FormTest extends WriterTestCase
{
	public function testWrite()
	{
		$writer = new Form();
		$actual = $writer->write($this->getRecord());

		$expect = <<<TEXT
id=1&author=foo&title=bar&content=foobar&date=2012-03-11T13%3A37%3A21%2B00%3A00
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testWriteResultSet()
	{
		$writer = new Form();
		$actual = $writer->write($this->getResultSet());

		$expect = <<<TEXT
totalResults=2&startIndex=0&itemsPerPage=8&entry%5B0%5D%5Bid%5D=1&entry%5B0%5D%5Bauthor%5D=foo&entry%5B0%5D%5Btitle%5D=bar&entry%5B0%5D%5Bcontent%5D=foobar&entry%5B0%5D%5Bdate%5D=2012-03-11T13%3A37%3A21%2B00%3A00&entry%5B1%5D%5Bid%5D=2&entry%5B1%5D%5Bauthor%5D=foo&entry%5B1%5D%5Btitle%5D=bar&entry%5B1%5D%5Bcontent%5D=foobar&entry%5B1%5D%5Bdate%5D=2012-03-11T13%3A37%3A21%2B00%3A00
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testIsContentTypeSupported()
	{
		$writer = new Form();

		$this->assertTrue($writer->isContentTypeSupported('application/x-www-form-urlencoded'));
		$this->assertFalse($writer->isContentTypeSupported('application/xml'));
	}

	public function testGetContentType()
	{
		$writer = new Form();

		$this->assertEquals('application/x-www-form-urlencoded', $writer->getContentType());
	}
}

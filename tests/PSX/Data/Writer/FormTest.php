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
 * FormTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FormTest extends WriterTestCase
{
	public function testWrite()
	{
		$writer = new Form();
		$actual = $writer->write($this->getRecord());

		$expect = <<<TEXT
id=1&author=foo&title=bar&content=foobar&date=2012-03-11T13%3A37%3A21Z
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testWriteResultSet()
	{
		$writer = new Form();
		$actual = $writer->write($this->getResultSet());

		$expect = <<<TEXT
totalResults=2&startIndex=0&itemsPerPage=8&entry%5B0%5D%5Bid%5D=1&entry%5B0%5D%5Bauthor%5D=foo&entry%5B0%5D%5Btitle%5D=bar&entry%5B0%5D%5Bcontent%5D=foobar&entry%5B0%5D%5Bdate%5D=2012-03-11T13%3A37%3A21Z&entry%5B1%5D%5Bid%5D=2&entry%5B1%5D%5Bauthor%5D=foo&entry%5B1%5D%5Btitle%5D=bar&entry%5B1%5D%5Bcontent%5D=foobar&entry%5B1%5D%5Bdate%5D=2012-03-11T13%3A37%3A21Z
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testWriteEmpty()
	{
		$writer = new Form();
		$actual = $writer->write($this->getEmptyRecord());

		$expect = <<<TEXT
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testIsContentTypeSupported()
	{
		$writer = new Form();

		$this->assertTrue($writer->isContentTypeSupported(new MediaType('application/x-www-form-urlencoded')));
		$this->assertFalse($writer->isContentTypeSupported(new MediaType('application/xml')));
	}

	public function testGetContentType()
	{
		$writer = new Form();

		$this->assertEquals('application/x-www-form-urlencoded', $writer->getContentType());
	}
}

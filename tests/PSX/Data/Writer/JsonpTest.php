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

namespace PSX\Data\Writer;

use PSX\Data\WriterTestCase;

/**
 * JsonpTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JsonpTest extends WriterTestCase
{
	public function testWrite()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');
		$actual = $writer->write($this->getRecord());

		$expect = <<<TEXT
foo({"id":1,"author":"foo","title":"bar","content":"foobar","date":"2012-03-11T13:37:21+00:00"})
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testWriteResultSet()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');
		$actual = $writer->write($this->getResultSet());

		$expect = <<<TEXT
foo({"totalResults":2,"startIndex":0,"itemsPerPage":8,"entry":[{"id":1,"author":"foo","title":"bar","content":"foobar","date":"2012-03-11T13:37:21+00:00"},{"id":2,"author":"foo","title":"bar","content":"foobar","date":"2012-03-11T13:37:21+00:00"}]})
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testWriteComplex()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');
		$actual = $writer->write($this->getComplexRecord());

		$expect = <<<TEXT
foo({"actor":{"displayName":"Martin Smith","id":"tag:example.org,2011:martin","objectType":"person","url":"http:\/\/example.org\/martin"},"object":{"id":"tag:example.org,2011:abc123\/xyz","url":"http:\/\/example.org\/blog\/2011\/02\/entry"},"target":{"displayName":"Martin's Blog","id":"tag:example.org,2011:abc123","objectType":"blog","url":"http:\/\/example.org\/blog\/"},"verb":"post","published":"2011-02-10T15:04:55+00:00"})
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testSetCallbackName()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');

		$this->assertEquals('foo', $writer->getCallbackName());
	}

	public function testSetCallbackNameInvalid()
	{
		$writer = new Jsonp();

		// invalid signs
		$writer->setCallbackName('!foo');

		$this->assertEquals(null, $writer->getCallbackName());

		// min length
		$writer->setCallbackName('fo');

		$this->assertEquals(null, $writer->getCallbackName());

		// max length
		$writer->setCallbackName('fooooooooooooooofoooooooooooooooo');

		$this->assertEquals(null, $writer->getCallbackName());
	}
}
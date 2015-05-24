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
 * JsonpTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
foo({
    "id": 1,
    "author": "foo",
    "title": "bar",
    "content": "foobar",
    "date": "2012-03-11T13:37:21Z"
})
TEXT;

		$this->assertJsonp($expect, $actual);
	}

	public function testWriteResultSet()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');
		$actual = $writer->write($this->getResultSet());

		$expect = <<<TEXT
foo({
    "totalResults": 2,
    "startIndex": 0,
    "itemsPerPage": 8,
    "entry": [
        {
            "id": 1,
            "author": "foo",
            "title": "bar",
            "content": "foobar",
            "date": "2012-03-11T13:37:21Z"
        },
        {
            "id": 2,
            "author": "foo",
            "title": "bar",
            "content": "foobar",
            "date": "2012-03-11T13:37:21Z"
        }
    ]
})
TEXT;

		$this->assertJsonp($expect, $actual);
	}

	public function testWriteComplex()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');
		$actual = $writer->write($this->getComplexRecord());

		$expect = <<<TEXT
foo({
    "verb": "post",
    "actor": {
        "id": "tag:example.org,2011:martin",
        "objectType": "person",
        "displayName": "Martin Smith",
        "url": "http:\/\/example.org\/martin"
    },
    "object": {
        "id": "tag:example.org,2011:abc123\/xyz",
        "url": "http:\/\/example.org\/blog\/2011\/02\/entry"
    },
    "target": {
        "id": "tag:example.org,2011:abc123",
        "objectType": "blog",
        "displayName": "Martin's Blog",
        "url": "http:\/\/example.org\/blog\/"
    },
    "published": "2011-02-10T15:04:55Z"
})
TEXT;

		$this->assertJsonp($expect, $actual);
	}

	public function testWriteEmpty()
	{
		$writer = new Jsonp();
		$writer->setCallbackName('foo');
		$actual = $writer->write($this->getEmptyRecord());

		$expect = <<<TEXT
foo({})
TEXT;

		$this->assertJsonp($expect, $actual);
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

	public function testIsContentTypeSupported()
	{
		$writer = new Jsonp();

		$this->assertTrue($writer->isContentTypeSupported(new MediaType('application/javascript')));
		$this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
	}

	public function testGetContentType()
	{
		$writer = new Jsonp();

		$this->assertEquals('application/javascript', $writer->getContentType());
	}

	public function testWriteEmptyCallback()
	{
		$writer = new Jsonp();
		$actual = $writer->write($this->getRecord());

		$expect = <<<TEXT
{
    "id": 1,
    "author": "foo",
    "title": "bar",
    "content": "foobar",
    "date": "2012-03-11T13:37:21Z"
}
TEXT;

		$this->assertJsonStringEqualsJsonString($expect, $actual);
	}

	protected function assertJsonp($expect, $actual)
	{
		preg_match('/^foo\((.*)\)$/s', $expect, $matchesExpect);
		preg_match('/^foo\((.*)\)$/s', $actual, $matchesActual);

		$this->assertJsonStringEqualsJsonString($matchesExpect[1], $matchesActual[1]);
	}
}
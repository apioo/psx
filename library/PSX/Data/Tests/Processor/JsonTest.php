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

namespace PSX\Data\Tests\Processor;

use PSX\Data\Payload;
use PSX\Data\Tests\Processor\Model\Comment;
use PSX\Data\Tests\Processor\Model\Entry;
use PSX\Data\Tests\Processor\Model\Person;
use PSX\Framework\Test\Environment;

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function testReadWriteJson()
    {
        $body = <<<JSON
{
	"id": "1",
	"title": "foo",
	"active": true,
	"count": 12,
	"rating": 4.8,
	"date": "2014-07-29T23:37:00Z",
	"person": {
	    "name": "foo",
	    "uri": "http://foo.com"
	},
	"tags": ["foo", "bar"],
	"comments": [{
	    "title": "foo",
	    "date": "2014-07-29T23:37:00Z"
	},{
	    "title": "bar",
	    "date": "2014-07-29T23:37:00Z"
	}]
}
JSON;

        $dm    = Environment::getService('io');
        $entry = $dm->read(Entry::class, Payload::json($body));

        $this->assertEquals(1, $entry->getId());
        $this->assertEquals('foo', $entry->getTitle());
        $this->assertEquals(true, $entry->isActive());
        $this->assertEquals(12, $entry->getCount());
        $this->assertEquals(4.8, $entry->getRating());
        $this->assertInstanceOf('DateTime', $entry->getDate());
        $this->assertEquals('Tue, 29 Jul 2014 23:37:00 +0000', $entry->getDate()->format('r'));
        $this->assertInstanceOf(Person::class, $entry->getPerson());
        $this->assertEquals('foo', $entry->getPerson()->getName());
        $this->assertEquals('http://foo.com', $entry->getPerson()->getUri());
        $this->assertEquals(['foo', 'bar'], $entry->getTags());
        $this->assertEquals(2, count($entry->getComments()));
        $this->assertContainsOnlyInstancesOf(Comment::class, $entry->getComments());
        $this->assertEquals('foo', $entry->getComments()[0]->getTitle());
        $this->assertEquals('bar', $entry->getComments()[1]->getTitle());

        $data = $dm->write(Payload::json($entry));

        $this->assertJsonStringEqualsJsonString($body, $data);
    }
}


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

namespace PSX\Json;

use PSX\Data\Record;
use stdClass;

/**
 * ComparatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ComparatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCompareArray()
    {
        $this->assertTrue(Comparator::compare([
        ], [
        ]));

        $this->assertTrue(Comparator::compare([
            'foo' => 'bar',
        ], [
            'foo' => 'bar',
        ]));

        $this->assertTrue(Comparator::compare([
            'foo' => [
                'bar' => 'foo',
                'foo' => 'bar',
            ]
        ], [
            'foo' => [
                'foo' => 'bar',
                'bar' => 'foo',
            ]
        ]));

        $this->assertFalse(Comparator::compare([
            'foo' => 'bar'
        ], [
        ]));

        $this->assertFalse(Comparator::compare([
        ], [
            'foo' => 'bar'
        ]));

        $this->assertFalse(Comparator::compare([
            'foo' => [
                'bar' => 'foo',
                'foo' => 'bar',
            ]
        ], [
            'foo' => [
                'foo' => 'bar',
            ]
        ]));

        $this->assertFalse(Comparator::compare([
            'foo' => 'bar',
        ], [
            'bar' => 'foo',
        ]));

        $this->assertFalse(Comparator::compare('foo', [
            'bar' => 'foo',
        ]));

        $this->assertFalse(Comparator::compare([
            'bar' => 'foo',
        ], 'foo'));
    }

    public function testCompareStdClass()
    {
        $this->assertTrue(Comparator::compare((object) [
        ], (object) [
        ]));

        $this->assertTrue(Comparator::compare((object) [
            'foo' => 'bar',
        ], (object) [
            'foo' => 'bar',
        ]));

        $this->assertTrue(Comparator::compare((object) [
            'foo' => (object) [
                'bar' => 'foo',
                'foo' => 'bar',
            ]
        ], (object) [
            'foo' => (object) [
                'foo' => 'bar',
                'bar' => 'foo',
            ]
        ]));

        $this->assertFalse(Comparator::compare((object) [
            'foo' => 'bar',
        ], (object) [
        ]));

        $this->assertFalse(Comparator::compare((object) [
        ], (object) [
            'foo' => 'bar',
        ]));

        $this->assertFalse(Comparator::compare((object) [
            'foo' => (object) [
                'bar' => 'foo',
                'foo' => 'bar',
            ]
        ], (object) [
            'foo' => (object) [
                'foo' => 'bar',
            ]
        ]));

        $this->assertFalse(Comparator::compare((object) [
            'foo' => 'bar',
        ], (object) [
            'bar' => 'foo',
        ]));

        $this->assertFalse(Comparator::compare('foo', (object) [
            'bar' => 'foo',
        ]));

        $this->assertFalse(Comparator::compare((object) [
            'bar' => 'foo',
        ], 'foo'));
    }

    public function testCompareRecord()
    {
        $this->assertTrue(Comparator::compare(Record::fromArray([
        ]), Record::fromArray([
        ])));

        $this->assertTrue(Comparator::compare(Record::fromArray([
            'foo' => 'bar',
        ]), Record::fromArray([
            'foo' => 'bar',
        ])));

        $this->assertTrue(Comparator::compare(Record::fromArray([
            'foo' => Record::fromArray([
                'bar' => 'foo',
                'foo' => 'bar',
            ])
        ]), Record::fromArray([
            'foo' => Record::fromArray([
                'foo' => 'bar',
                'bar' => 'foo',
            ])
        ])));

        $this->assertFalse(Comparator::compare(Record::fromArray([
            'foo' => 'bar'
        ]), Record::fromArray([
        ])));

        $this->assertFalse(Comparator::compare(Record::fromArray([
        ]), Record::fromArray([
            'foo' => 'bar'
        ])));

        $this->assertFalse(Comparator::compare(Record::fromArray([
            'foo' => Record::fromArray([
                'bar' => 'foo',
                'foo' => 'bar',
            ])
        ]), Record::fromArray([
            'foo' => Record::fromArray([
                'foo' => 'bar',
            ])
        ])));

        $this->assertFalse(Comparator::compare(Record::fromArray([
            'foo' => 'bar',
        ]), Record::fromArray([
            'bar' => 'foo',
        ])));

        $this->assertFalse(Comparator::compare('foo', Record::fromArray([
            'bar' => 'foo',
        ])));

        $this->assertFalse(Comparator::compare(Record::fromArray([
            'bar' => 'foo',
        ]), 'foo'));
    }
}

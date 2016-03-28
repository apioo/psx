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

namespace PSX\Data\Tests;

use PSX\Data\Record;

/**
 * RecordTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordTest extends \PHPUnit_Framework_TestCase
{
    public function testGetProperty()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->getProperty('id'));
        $this->assertEquals('bar', $record->getProperty('title'));
    }

    public function testSetProperty()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->getProperty('id'));

        $record->setProperty('id', 2);

        $this->assertEquals(2, $record->getProperty('id'));
    }

    public function testRemoveProperty()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertTrue($record->hasProperty('id'));

        $record->removeProperty('id');

        $this->assertFalse($record->hasProperty('id'));
    }

    public function testHasProperty()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertTrue($record->hasProperty('id'));
    }

    public function testOffsetSet()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record['id']);

        $record['id'] = 2;

        $this->assertEquals(2, $record['id']);
    }

    public function testOffsetExists()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertTrue(isset($record['id']));
    }

    public function testOffsetUnset()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertTrue(isset($record['id']));

        unset($record['id']);

        $this->assertFalse(isset($record['id']));
    }

    public function testGetMagicGetter()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->id);
        $this->assertEquals('bar', $record->title);
    }

    public function testOffsetGet()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record['id']);
        $this->assertEquals('bar', $record['title']);
    }

    public function testSetMagicSetter()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->id);
        $this->assertEquals('bar', $record->title);

        $record->id = 2;
        $record->title = 'foo';

        $this->assertEquals(2, $record->id);
        $this->assertEquals('foo', $record->title);
    }

    public function testGetProperties()
    {
        $fields = array(
            'id'    => 1,
            'title' => 'bar',
        );
        $record = new Record('foo', $fields);

        $this->assertEquals($fields, $record->getProperties());
        $this->assertEquals('foo', $record->getDisplayName());
    }

    public function testSerialize()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->id);
        $this->assertEquals('bar', $record->title);

        $record = unserialize(serialize($record));

        $this->assertEquals(1, $record->id);
        $this->assertEquals('bar', $record->title);
    }

    public function testJsonEncode()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $expect = '{"id": 1, "title": "bar"}';

        $this->assertJsonStringEqualsJsonString($expect, json_encode($record));
    }

    public function testBadProperty()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $record->foo;
    }

    public function testFromArray()
    {
        $record = Record::fromArray([
            'id'    => 1,
            'title' => 'bar',
        ]);

        $this->assertInstanceOf('PSX\Data\RecordInterface', $record);
        $this->assertEquals(1, $record->id);
        $this->assertEquals('bar', $record->title);
    }

    public function testFromStdClass()
    {
        $record = Record::fromStdClass((object) [
            'id'    => 1,
            'title' => 'bar',
        ]);

        $this->assertInstanceOf('PSX\Data\RecordInterface', $record);
        $this->assertEquals(1, $record->id);
        $this->assertEquals('bar', $record->title);
    }

    public function testFrom()
    {
        $record = Record::from(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], Record::from(['foo' => 'bar'])->getProperties());
        $this->assertEquals(['foo' => 'bar'], Record::from((object) ['foo' => 'bar'])->getProperties());
        $this->assertEquals(['foo' => 'bar'], Record::from($record)->getProperties());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromInvalid()
    {
        Record::from('foo');
    }

    public function testMerge()
    {
        $left   = Record::fromArray(['id' => 1, 'foo' => 'bar']);
        $right  = Record::fromArray(['foo' => 'foo']);
        $result = Record::merge($left, $right);

        $this->assertInstanceOf('PSX\Data\RecordInterface', $result);
        $this->assertEquals(['id' => 1, 'foo' => 'foo'], $result->getProperties());
    }
}

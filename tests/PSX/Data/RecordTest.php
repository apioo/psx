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

namespace PSX\Data;

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

    public function testGetMagicMethods()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->getId());
        $this->assertEquals('bar', $record->getTitle());
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

    public function testSetMagicMethods()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->getId());
        $this->assertEquals('bar', $record->getTitle());

        $record->setId(2);
        $record->setTitle('foo');

        $this->assertEquals(2, $record->getId());
        $this->assertEquals('foo', $record->getTitle());
    }

    public function testGetRecordInfo()
    {
        $fields = array(
            'id'    => 1,
            'title' => 'bar',
        );
        $record = new Record('foo', $fields);

        $this->assertEquals('foo', $record->getRecordInfo()->getName());
        $this->assertEquals($fields, $record->getRecordInfo()->getFields());
        $this->assertEquals(true, $record->getRecordInfo()->hasField('id'));
    }

    public function testSerialize()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $this->assertEquals(1, $record->getId());
        $this->assertEquals('bar', $record->getTitle());

        $record = unserialize(serialize($record));

        $this->assertEquals(1, $record->getId());
        $this->assertEquals('bar', $record->getTitle());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBadMethodCall()
    {
        $record = new Record('foo', array(
            'id'    => 1,
            'title' => 'bar',
        ));

        $record->foo();
    }
}

class StringObject
{
    public function __toString()
    {
        return 'foo';
    }
}

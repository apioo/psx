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

namespace PSX\Data;

/**
 * CollectionAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CollectionAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $collection = new TestCollection(array(new Record(), new Record()));
        $collection->add(new Record());

        $this->assertInstanceOf('PSX\Data\CollectionInterface', $collection);
        $this->assertEquals(3, $collection->count());
        $this->assertEquals(3, count($collection));
        $this->assertFalse($collection->isEmpty());

        $this->assertEquals(null, $collection->get(3));

        $collection->set(3, new Record());

        $this->assertInstanceOf('PSX\Data\RecordInterface', $collection->get(3));

        foreach ($collection as $record) {
            $this->assertInstanceOf('PSX\Data\RecordInterface', $record);
        }

        $collection->clear();

        $this->assertEquals(0, $collection->count());
        $this->assertEquals(array(), $collection->toArray());
        $this->assertTrue($collection->isEmpty());
    }
}

class TestCollection extends CollectionAbstract
{
    public function getRecordInfo()
    {
        return new RecordInfo('foo', array());
    }
}

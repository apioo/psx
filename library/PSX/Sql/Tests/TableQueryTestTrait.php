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

namespace PSX\Sql\Tests;

use PSX\Data\Record;
use PSX\Sql\Condition;
use PSX\Sql\Fields;
use PSX\Sql\Sql;
use PSX\Sql\TableQueryInterface;

/**
 * TableQueryTestTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait TableQueryTestTrait
{
    public function testGetAll()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll();

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(4, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 4,
                'userId' => 3,
                'title' => 'blub',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 3,
                'userId' => 2,
                'title' => 'test',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllStartIndex()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(3);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(1, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllCount()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(0, 2);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 4,
                'userId' => 3,
                'title' => 'blub',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 3,
                'userId' => 2,
                'title' => 'test',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllStartIndexAndCountDefault()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(2, 2);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllStartIndexAndCountDesc()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(2, 2, 'id', Sql::SORT_DESC);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllStartIndexAndCountAsc()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(2, 2, 'id', Sql::SORT_ASC);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 3,
                'userId' => 2,
                'title' => 'test',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 4,
                'userId' => 3,
                'title' => 'blub',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllSortDesc()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(0, 2, 'id', Sql::SORT_DESC);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 4,
                'userId' => 3,
                'title' => 'blub',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 3,
                'userId' => 2,
                'title' => 'test',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);

        foreach ($result as $row) {
            $this->assertTrue($row->id != null);
            $this->assertTrue($row->title != null);
        }

        // check order
        $this->assertEquals(4, $result[0]->id);
        $this->assertEquals(3, $result[1]->id);
    }

    public function testGetAllSortAsc()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(0, 2, 'id', Sql::SORT_ASC);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllCondition()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $con    = new Condition(array('userId', '=', 1));
        $result = $table->getAll(0, 16, 'id', Sql::SORT_DESC, $con);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllConditionAndConjunction()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $con = new Condition();
        $con->add('userId', '=', 1, 'AND');
        $con->add('userId', '=', 3);
        $result = $table->getAll(0, 16, 'id', Sql::SORT_DESC, $con);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(0, count($result));

        // check and condition with result
        $con = new Condition();
        $con->add('userId', '=', 1, 'AND');
        $con->add('title', '=', 'foo');
        $result = $table->getAll(0, 16, 'id', Sql::SORT_DESC, $con);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(1, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllConditionOrConjunction()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $con = new Condition();
        $con->add('userId', '=', 1, 'OR');
        $con->add('userId', '=', 3);
        $result = $table->getAll(0, 16, 'id', Sql::SORT_DESC, $con);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(3, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 4,
                'userId' => 3,
                'title' => 'blub',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllFieldWhitelist()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(0, 2, 'id', Sql::SORT_DESC, null, Fields::whitelist(['id', 'title']));

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 4,
                'title' => 'blub',
            )),
            new Record('comment', array(
                'id' => 3,
                'title' => 'test',
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetAllFieldBlacklist()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getAll(0, 2, 'id', Sql::SORT_DESC, null, Fields::blacklist(['id', 'title']));

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'userId' => 3,
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'userId' => 2,
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetBy()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getByUserId(1);

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 2,
                'userId' => 1,
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetByFieldWhitelist()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $result = $table->getByUserId(1, Fields::whitelist(['id', 'title']));

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(2, count($result));

        $expect = array(
            new Record('comment', array(
                'id' => 2,
                'title' => 'bar',
            )),
            new Record('comment', array(
                'id' => 1,
                'title' => 'foo',
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testGetOneBy()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $row = $table->getOneById(1);

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, array($row));
    }

    public function testGetOneByFieldWhitelist()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $row = $table->getOneById(1, Fields::whitelist(['id', 'title']));

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'title' => 'foo',
            )),
        );

        $this->assertEquals($expect, array($row));
    }

    public function testGet()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $row = $table->get(1);

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'userId' => 1,
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, array($row));
    }

    public function testGetFieldWhitelist()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $row = $table->get(1, Fields::whitelist(['id', 'title']));

        $expect = array(
            new Record('comment', array(
                'id' => 1,
                'title' => 'foo',
            )),
        );

        $this->assertEquals($expect, array($row));
    }

    public function testGetSupportedFields()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $fields = $table->getSupportedFields();

        $this->assertEquals(array('id', 'userId', 'title', 'date'), $fields);
    }

    public function testGetCount()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $this->assertEquals(4, $table->getCount());
        $this->assertEquals(2, $table->getCount(new Condition(array('userId', '=', 1))));
        $this->assertEquals(1, $table->getCount(new Condition(array('userId', '=', 3))));
    }

    public function testGetRecord()
    {
        $table = $this->getTable();

        if (!$table instanceof TableQueryInterface) {
            $this->markTestSkipped('Table not an query interface');
        }

        $obj = $table->getRecord();

        $this->assertInstanceOf('PSX\Data\RecordInterface', $obj);
        $this->assertEquals('record', $obj->getDisplayName());
    }

    public function testRestrictedFields()
    {
        $table = $this->getTable();
        $table->setRestrictedFields(array('id', 'userId'));

        $result = $table->getAll();

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(4, count($result));
        $this->assertEquals(array(2 => 'title', 3 => 'date'), $table->getSupportedFields());

        $table->setRestrictedFields(array());

        $expect = array(
            new Record('comment', array(
                'title' => 'blub',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'title' => 'test',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'title' => 'bar',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
            new Record('comment', array(
                'title' => 'foo',
                'date' => new \DateTime('2013-04-29 16:56:32'),
            )),
        );

        $this->assertEquals($expect, $result);
    }

    public function testNestedResult()
    {
        $result = $this->getTable()->getNestedResult();

        $expect = array(
            new Record('comment', array(
                'id' => 4,
                'author' => (object) array('userId' => 3, 'date' => new \DateTime('2013-04-29 16:56:32')),
                'title' => 'blub',
            )),
            new Record('comment', array(
                'id' => 3,
                'author' => (object) array('userId' => 2, 'date' => new \DateTime('2013-04-29 16:56:32')),
                'title' => 'test',
            )),
            new Record('comment', array(
                'id' => 2,
                'author' => (object) array('userId' => 1, 'date' => new \DateTime('2013-04-29 16:56:32')),
                'title' => 'bar',
            )),
            new Record('comment', array(
                'id' => 1,
                'author' => (object) array('userId' => 1, 'date' => new \DateTime('2013-04-29 16:56:32')),
                'title' => 'foo',
            )),
        );

        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOneByXXXMethodNoValue()
    {
        $this->getTable()->getOneById();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetByXXXMethodNoValue()
    {
        $this->getTable()->getById();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testInvalidMethodCall()
    {
        $this->getTable()->foobar();
    }
}

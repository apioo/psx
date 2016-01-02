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

namespace PSX\Sql;

use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Test\DbTestCase;
use PSX\Test\Environment;

/**
 * TableAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableAbstractTest extends DbTestCase
{
    use TableTestCase;

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/table_fixture.xml');
    }

    /**
     * @return \PSX\Sql\TableInterface
     */
    protected function getTable()
    {
        return Environment::getService('table_manager')->getTable('PSX\Sql\TestTable');
    }

    public function testGetName()
    {
        $this->assertEquals('psx_handler_comment', $this->getTable()->getName());
    }

    public function testGetColumns()
    {
        $expect = array(
            'id'     => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
            'userId' => TableInterface::TYPE_INT | 10,
            'title'  => TableInterface::TYPE_VARCHAR | 32,
            'date'   => TableInterface::TYPE_DATETIME,
        );

        $this->assertEquals($expect, $this->getTable()->getColumns());
    }

    public function testGetConnections()
    {
        $this->assertEquals(array(), $this->getTable()->getConnections());
    }

    public function testGetDisplayName()
    {
        $this->assertEquals('comment', $this->getTable()->getDisplayName());
    }

    public function testGetPrimaryKey()
    {
        $this->assertEquals('id', $this->getTable()->getPrimaryKey());
    }

    public function testHasColumn()
    {
        $this->assertTrue($this->getTable()->hasColumn('title'));
        $this->assertFalse($this->getTable()->hasColumn('foobar'));
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

    /**
     * @expectedException \PSX\Exception
     */
    public function testUpdateNoPrimaryKey()
    {
        $table = new Table($this->connection, 'psx_handler_comment', array('foo' => TableInterface::TYPE_VARCHAR));
        $table->update(array('foo' => 'bar'));
    }

    /**
     * @expectedException \PSX\Exception
     */
    public function testDeleteNoPrimaryKey()
    {
        $table = new Table($this->connection, 'psx_handler_comment', array('foo' => TableInterface::TYPE_VARCHAR));
        $table->delete(array('foo' => 'bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidData()
    {
        $table = new Table($this->connection, 'psx_handler_comment', array('foo' => TableInterface::TYPE_VARCHAR));
        $table->create('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateInvalidData()
    {
        $table = new Table($this->connection, 'psx_handler_comment', array('foo' => TableInterface::TYPE_VARCHAR));
        $table->update('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteInvalidData()
    {
        $table = new Table($this->connection, 'psx_handler_comment', array('foo' => TableInterface::TYPE_VARCHAR));
        $table->delete('foo');
    }
}

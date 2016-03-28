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

use DateTime;
use PSX\Sql\Table;
use PSX\Sql\TableInterface;
use PSX\Sql\TableManipulationInterface;

/**
 * TableManipulationTestTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait TableManipulationTestTrait
{
    public function testCreate()
    {
        $table = $this->getTable();

        if (!$table instanceof TableManipulationInterface) {
            $this->markTestSkipped('Table not an manipulation interface');
        }

        $record = $table->getRecord();
        $record->id = 5;
        $record->userId = 2;
        $record->title = 'foobar';
        $record->date = new DateTime();

        $table->create($record);

        $this->assertEquals(5, $table->getLastInsertId());

        $row = $table->getOneById(5);

        $this->assertInstanceOf('PSX\Data\RecordInterface', $row);
        $this->assertEquals(5, $row->id);
        $this->assertEquals(2, $row->userId);
        $this->assertEquals('foobar', $row->title);
        $this->assertInstanceOf('DateTime', $row->date);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateEmpty()
    {
        $table = $this->getTable();

        if (!$table instanceof TableManipulationInterface) {
            $this->markTestSkipped('Table not an manipulation interface');
        }

        $table->create(array());
    }

    public function testUpdate()
    {
        $table = $this->getTable();

        if (!$table instanceof TableManipulationInterface) {
            $this->markTestSkipped('Table not an manipulation interface');
        }

        $row = $table->getOneById(1);
        $row->userId = 2;
        $row->title = 'foobar';
        $row->date = new DateTime();

        $table->update($row);

        $row = $table->getOneById(1);

        $this->assertEquals(2, $row->userId);
        $this->assertEquals('foobar', $row->title);
        $this->assertInstanceOf('DateTime', $row->date);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateEmpty()
    {
        $table = $this->getTable();

        if (!$table instanceof TableManipulationInterface) {
            $this->markTestSkipped('Table not an manipulation interface');
        }

        $table->update(array());
    }

    public function testDelete()
    {
        $table = $this->getTable();

        if (!$table instanceof TableManipulationInterface) {
            $this->markTestSkipped('Table not an manipulation interface');
        }

        $row = $table->getOneById(1);

        $table->delete($row);

        $row = $table->getOneById(1);

        $this->assertEmpty($row);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDeleteEmpty()
    {
        $table = $this->getTable();

        if (!$table instanceof TableManipulationInterface) {
            $this->markTestSkipped('Table not an manipulation interface');
        }

        $table->delete(array());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUpdateNoPrimaryKey()
    {
        $table = new Table($this->connection, 'psx_handler_comment', array('foo' => TableInterface::TYPE_VARCHAR));
        $table->update(array('foo' => 'bar'));
    }

    /**
     * @expectedException \RuntimeException
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

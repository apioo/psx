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

use PSX\Framework\Test\DbTestCase;
use PSX\Sql\Table\Reader;
use PSX\Sql\TableManager;

/**
 * TableManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableManagerTest extends DbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/table_fixture.xml');
    }

    public function testGetTable()
    {
        $manager = new TableManager($this->connection);

        $table = $manager->getTable('PSX\Sql\Tests\TestTable');

        $this->assertInstanceOf('PSX\Sql\TableInterface', $table);
        $this->assertEquals('psx_handler_comment', $table->getName());
        $this->assertEquals(['id', 'userId', 'title', 'date'], array_keys($table->getColumns()));
    }

    public function testGetTableWithReader()
    {
        $manager = new TableManager($this->connection, new Reader\Schema($this->connection));

        $table = $manager->getTable('psx_handler_comment');

        $this->assertInstanceOf('PSX\Sql\TableInterface', $table);
        $this->assertEquals('psx_handler_comment', $table->getName());
        $this->assertEquals(['id', 'userId', 'title', 'date'], array_keys($table->getColumns()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTableInvalidTable()
    {
        $manager = new TableManager($this->connection);
        $manager->getTable('PSX\Sql\FooTable');
    }

    public function testGetConnection()
    {
        $manager = new TableManager($this->connection);

        $this->assertInstanceOf('Doctrine\DBAL\Connection', $manager->getConnection());
    }
}

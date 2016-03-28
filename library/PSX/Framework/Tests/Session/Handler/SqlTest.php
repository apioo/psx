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

namespace PSX\Framework\Tests\Session\Handler;

use PSX\Framework\Session\Handler\Sql;
use PSX\Sql\Table;
use PSX\Sql\Table\ColumnAllocation;
use PSX\Framework\Test\DbTestCase;

/**
 * SqlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SqlTest extends DbTestCase
{
    protected $table = 'psx_session_handler_sql_test';

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/handler_fixture.xml');
    }

    public function testOpen()
    {
        $this->getHandler()->open('/', 'file');
    }

    public function testClose()
    {
        $this->getHandler()->close();
    }

    public function testRead()
    {
        $data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

        $this->assertEquals('foobar', $data);

        $data = $this->getHandler()->read('unknown');

        $this->assertEquals(null, $data);
    }

    public function testWrite()
    {
        $this->getHandler()->write('eae84a980e3bcd9bb04cb866facc9385', 'foobar2');

        $this->assertEquals('foobar2', $this->getHandler()->read('eae84a980e3bcd9bb04cb866facc9385'));
    }

    public function testDestroy()
    {
        $data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

        $this->assertEquals('foobar', $data);

        $this->getHandler()->destroy('0bb3df120bff3c64e9ef553b61ffcd06');

        $data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

        $this->assertEquals(null, $data);
    }

    public function testGc()
    {
        $data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

        $this->assertEquals('foobar', $data);

        $this->getHandler()->gc(1);

        $data = $this->getHandler()->read('0bb3df120bff3c64e9ef553b61ffcd06');

        $this->assertEquals(null, $data);
    }

    protected function getHandler()
    {
        $allocation = new ColumnAllocation(array(
            Sql::COLUMN_ID      => 'id',
            Sql::COLUMN_CONTENT => 'content',
            Sql::COLUMN_DATE    => 'date',
        ));

        return new Sql($this->connection, $this->table, $allocation);
    }
}

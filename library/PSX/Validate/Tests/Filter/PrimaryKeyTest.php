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

namespace PSX\Validate\Tests\Filter;

use PSX\Framework\Test\DbTestCase;
use PSX\Framework\Test\Environment;
use PSX\Sql\Table;
use PSX\Sql\TableInterface;
use PSX\Validate\Filter\PrimaryKey;

/**
 * PrimaryKeyTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PrimaryKeyTest extends DbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../../Sql/Tests/table_fixture.xml');
    }

    public function testFilter()
    {
        $table  = Environment::getService('table_manager')->getTable('PSX\Sql\Tests\TestTable');
        $filter = new PrimaryKey($table);

        $this->assertEquals(true, $filter->apply(1));
        $this->assertEquals(false, $filter->apply(32));

        // test error message
        $this->assertEquals('%s does not exist in table', $filter->getErrorMessage());
    }

    public function testFilterTableWithoutPk()
    {
        $table  = new Table($this->connection, 'psx_handler_comment', array('name' => TableInterface::TYPE_VARCHAR));
        $filter = new PrimaryKey($table);

        $this->assertEquals(false, $filter->apply(1));
        $this->assertEquals(false, $filter->apply(32));
    }
}

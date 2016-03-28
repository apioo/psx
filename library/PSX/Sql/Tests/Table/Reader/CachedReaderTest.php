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

namespace PSX\Sql\Tests\Table\Reader;

use Doctrine\Common\Cache\ArrayCache;
use PSX\Cache\Pool;
use PSX\DateTime\DateTime;
use PSX\Framework\Test\DbTestCase;
use PSX\Framework\Test\TableDataSet;
use PSX\Sql\Table;
use PSX\Sql\Table\Reader\Schema;
use PSX\Sql\Table\Reader\CachedReader;
use PSX\Sql\TableInterface;

/**
 * CachedReaderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedReaderTest extends DbTestCase
{
    public function getDataSet()
    {
        $table = new Table($this->connection, 'psx_sql_table_test', array(
            'id'    => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
            'title' => TableInterface::TYPE_VARCHAR | 32,
            'date'  => TableInterface::TYPE_DATETIME,
        ));

        $dataSet = new TableDataSet();
        $dataSet->addTable($table, array(
            array('id' => null, 'title' => 'foo', 'date' => date(DateTime::SQL)),
        ));

        return $dataSet;
    }

    public function testGetTableDefinition()
    {
        $cache  = new Pool(new ArrayCache());
        $reader = new CachedReader(new Schema($this->connection), $cache);
        $table  = $reader->getTableDefinition('psx_sql_table_test');

        $this->assertEquals('psx_sql_table_test', $table->getName());

        $columns = $table->getColumns();

        $this->assertEquals(TableInterface::TYPE_INT | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
        $this->assertEquals(TableInterface::TYPE_VARCHAR | 32, $columns['title']);
        $this->assertEquals(TableInterface::TYPE_DATETIME, $columns['date']);

        // next call should go through the cache
        $table  = $reader->getTableDefinition('psx_sql_table_test');

        $this->assertEquals('psx_sql_table_test', $table->getName());

        $columns = $table->getColumns();

        $this->assertEquals(TableInterface::TYPE_INT | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
        $this->assertEquals(TableInterface::TYPE_VARCHAR | 32, $columns['title']);
        $this->assertEquals(TableInterface::TYPE_DATETIME, $columns['date']);
    }
}

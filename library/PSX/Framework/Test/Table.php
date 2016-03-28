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

namespace PSX\Framework\Test;

use PSX\Framework\Exception;
use PSX\Sql\TableInterface;

/**
 * Table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Table implements \PHPUnit_Extensions_Database_DataSet_ITable
{
    private $table;
    private $data;

    public function __construct(TableInterface $table, array $data)
    {
        $this->table = $table;
        $this->data  = $data;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTableMetaData()
    {
        return new TableMetaData($this->table);
    }

    public function getRowCount()
    {
        return count($this->data);
    }

    public function getValue($row, $column)
    {
        return $this->data[$row][$column];
    }

    public function getRow($row)
    {
        return $this->data[$row];
    }

    public function matches(\PHPUnit_Extensions_Database_DataSet_ITable $other)
    {
        return $this->table->getName() == $other->getTable()->getTableName();
    }
}

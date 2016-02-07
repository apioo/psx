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

namespace PSX\Sql\Table\Reader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use PSX\Sql;
use PSX\Sql\SerializeTrait;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;
use PSX\Sql\TableInterface;

/**
 * Schema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Schema implements ReaderInterface
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTableDefinition($tableName)
    {
        $sm = $this->connection->getSchemaManager();

        // columns
        $table   = $sm->listTableDetails($tableName);
        $columns = array();

        foreach ($table->getColumns() as $column) {
            $columns[$column->getName()] = $this->getType($column);
        }

        // set primary key
        $pk        = $table->getPrimaryKey();
        $pkColumns = $pk->getColumns();

        if (count($pkColumns) == 1 && $pk->isPrimary()) {
            $pkColumn = $pkColumns[0];

            if (isset($columns[$pkColumn])) {
                $columns[$pkColumn] = $columns[$pkColumn] | TableInterface::PRIMARY_KEY;
            }
        }

        return new Definition($tableName, $columns);
    }

    protected function getType(Column $column)
    {
        $type = 0;

        if ($column->getLength() > 0) {
            $type+= $column->getLength();
        }

        $type = $type | SerializeTrait::getTypeByDoctrineType($column->getType());

        if (!$column->getNotnull()) {
            $type = $type | TableInterface::IS_NULL;
        }

        if ($column->getAutoincrement()) {
            $type = $type | TableInterface::AUTO_INCREMENT;
        }

        return $type;
    }
}

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

use Doctrine\DBAL\Connection;

/**
 * TableAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TableAbstract implements TableInterface
{
    use SerializeTrait;
    use TableQueryTrait;
    use TableManipulationTrait;

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getDisplayName()
    {
        $name = $this->getName();
        $pos  = strrpos($name, '_');

        return $pos !== false ? substr($name, strrpos($name, '_') + 1) : $name;
    }

    public function getPrimaryKey()
    {
        return $this->getFirstColumnWithAttr(self::PRIMARY_KEY);
    }

    public function hasColumn($column)
    {
        $columns = $this->getColumns();

        return isset($columns[$column]);
    }

    protected function getFirstColumnWithAttr($searchAttr)
    {
        $columns = $this->getColumns();

        foreach ($columns as $column => $attr) {
            if ($attr & $searchAttr) {
                return $column;
            }
        }

        return null;
    }
}

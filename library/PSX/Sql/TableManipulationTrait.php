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

use InvalidArgumentException;
use PSX\Data\RecordInterface;
use RuntimeException;

/**
 * TableManipulationTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait TableManipulationTrait
{
    protected $lastInsertId;

    public function create($record)
    {
        $fields = $this->serializeFields($this->getArray($record));

        if (!empty($fields)) {
            $result = $this->connection->insert($this->getName(), $fields);

            // set last insert id
            $this->lastInsertId = $this->connection->lastInsertId();

            return $result;
        } else {
            throw new RuntimeException('No valid field set');
        }
    }

    public function update($record)
    {
        $fields = $this->serializeFields($this->getArray($record));

        if (!empty($fields)) {
            $pk = $this->getPrimaryKey();

            if (isset($fields[$pk])) {
                $condition = array($pk => $fields[$pk]);
            } else {
                throw new RuntimeException('No primary key set');
            }

            return $this->connection->update($this->getName(), $fields, $condition);
        } else {
            throw new RuntimeException('No valid field set');
        }
    }

    public function delete($record)
    {
        $fields = $this->serializeFields($this->getArray($record));

        if (!empty($fields)) {
            $pk = $this->getPrimaryKey();

            if (isset($fields[$pk])) {
                $condition = array($pk => $fields[$pk]);
            } else {
                throw new RuntimeException('No primary key set');
            }

            return $this->connection->delete($this->getName(), $condition);
        } else {
            throw new RuntimeException('No valid field set');
        }
    }

    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * Returns an array which can be used by the dbal insert, update and delete
     * methods
     *
     * @param array $row
     * @return array
     */
    protected function serializeFields(array $row)
    {
        $data    = array();
        $columns = $this->getColumns();

        foreach ($columns as $name => $type) {
            if (isset($row[$name])) {
                $data[$name] = $this->serializeType($row[$name], $type);
            }
        }

        return $data;
    }

    protected function getArray($record)
    {
        if ($record instanceof RecordInterface) {
            return $record->getProperties();
        } elseif ($record instanceof \stdClass) {
            return (array) $record;
        } elseif (is_array($record)) {
            return $record;
        } else {
            throw new InvalidArgumentException('Record must bei either an PSX\Data\RecordInterface or array');
        }
    }
}

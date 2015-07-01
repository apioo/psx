<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Exception;
use PSX\Sql;

/**
 * TableAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TableAbstract extends TableQueryAbstract implements TableInterface
{
    use SerializeTrait;

    protected $connection;
    protected $select;
    protected $lastInsertId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnections()
    {
        return array();
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

    public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
    {
        $startIndex = $startIndex !== null ? (int) $startIndex : 0;
        $count      = !empty($count)       ? (int) $count      : 16;
        $sortBy     = $sortBy     !== null ? $sortBy           : $this->getPrimaryKey();
        $sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

        if (!in_array($sortBy, $this->getSupportedFields())) {
            $sortBy = $this->getPrimaryKey();
        }

        $fields  = $this->getSupportedFields();
        $builder = $this->connection->createQueryBuilder()
            ->select($fields)
            ->from($this->getName(), null)
            ->orderBy($sortBy, $sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC')
            ->setFirstResult($startIndex)
            ->setMaxResults($count);

        if ($condition !== null && $condition->hasCondition()) {
            $builder->where(substr($condition->getStatment(), 5));

            $values = $condition->getValues();
            foreach ($values as $key => $value) {
                $builder->setParameter($key, $value);
            }
        }

        return $this->project($builder->getSQL(), $builder->getParameters());
    }

    public function get($id)
    {
        $condition = new Condition(array($this->getPrimaryKey(), '=', $id));

        return $this->getOneBy($condition);
    }

    public function getCount(Condition $condition = null)
    {
        $builder = $this->connection->createQueryBuilder()
            ->select($this->connection->getDatabasePlatform()->getCountExpression($this->getPrimaryKey()))
            ->from($this->getName(), null);

        if ($condition !== null && $condition->hasCondition()) {
            $builder->where(substr($condition->getStatment(), 5));

            $values = $condition->getValues();
            foreach ($values as $key => $value) {
                $builder->setParameter($key, $value);
            }
        }

        return (int) $this->connection->fetchColumn($builder->getSQL(), $builder->getParameters());
    }

    public function getSupportedFields()
    {
        return array_diff(array_keys($this->getColumns()), $this->getRestrictedFields());
    }

    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    public function create($record)
    {
        $fields = $this->serializeFields($this->getArray($record));

        if (!empty($fields)) {
            $result = $this->connection->insert($this->getName(), $fields);

            // set last insert id
            $this->lastInsertId = $this->connection->lastInsertId();

            return $result;
        } else {
            throw new Exception('No valid field set');
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
                throw new Exception('No primary key set');
            }

            return $this->connection->update($this->getName(), $fields, $condition);
        } else {
            throw new Exception('No valid field set');
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
                throw new Exception('No primary key set');
            }

            return $this->connection->delete($this->getName(), $condition);
        } else {
            throw new Exception('No valid field set');
        }
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

    protected function getFirstColumnWithType($searchType)
    {
        $columns = $this->getColumns();

        foreach ($columns as $column => $attr) {
            if (((($attr >> 20) & 0xFF) << 20) === $searchType) {
                return $column;
            }
        }

        return null;
    }

    protected function project($sql, array $params = array(), array $columns = null, NestRule $nestRule = null)
    {
        $result  = array();
        $columns = $columns === null ? $this->getColumns() : $columns;
        $stmt    = $this->connection->executeQuery($sql, $params ?: array());
        $name    = $this->getDisplayName();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($row as $key => $value) {
                if (isset($columns[$key])) {
                    $value = $this->unserializeType($value, $columns[$key]);
                }

                if ($nestRule !== null) {
                    $parentKey = $nestRule->getParent($key);

                    if ($parentKey !== null) {
                        if (!isset($row[$parentKey])) {
                            $row[$parentKey] = new \stdClass();
                        }

                        $row[$parentKey]->$key = $value;

                        unset($row[$key]);
                    } else {
                        $row[$key] = $value;
                    }
                } else {
                    $row[$key] = $value;
                }
            }

            $result[] = new Record($name, $row);
        }

        $stmt->closeCursor();

        return $result;
    }

    protected function getArray($record)
    {
        if ($record instanceof RecordInterface) {
            return $record->getRecordInfo()->getData();
        } elseif ($record instanceof \stdClass) {
            return (array) $record;
        } elseif (is_array($record)) {
            return $record;
        } else {
            throw new InvalidArgumentException('Record must bei either an PSX\Data\RecordInterface or array');
        }
    }
}

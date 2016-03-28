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

use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\Record;

/**
 * TableQueryTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait TableQueryTrait
{
    protected $restrictedFields = array();

    public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null, Fields $fields = null)
    {
        $startIndex = $startIndex !== null ? (int) $startIndex : 0;
        $count      = !empty($count)       ? (int) $count      : 16;
        $sortBy     = $sortBy     !== null ? $sortBy           : $this->getPrimaryKey();
        $sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

        if ($fields !== null) {
            $fieldsWhitelist = $fields->getWhitelist();
            $fieldsBlacklist = $fields->getBlacklist();
        } else {
            $fieldsWhitelist = null;
            $fieldsBlacklist = null;
        }

        $columns = array_keys($this->getColumns());

        if (!empty($fieldsWhitelist)) {
            $columns = array_intersect($columns, $fieldsWhitelist);
        } elseif (!empty($fieldsBlacklist)) {
            $columns = array_diff($columns, $fieldsBlacklist);
        } else {
            $columns = array_diff($columns, $this->getRestrictedFields());
        }

        if (!in_array($sortBy, $columns)) {
            $sortBy = $this->getPrimaryKey();
        }

        list($sql, $parameters) = $this->getQuery(
            $this->getName(), 
            $columns, 
            $startIndex, 
            $count, 
            $sortBy, 
            $sortOrder, 
            $condition
        );

        return $this->project($sql, $parameters);
    }

    public function getBy(Condition $condition, Fields $fields = null)
    {
        return $this->getAll(null, null, null, null, $condition, $fields);
    }

    public function getOneBy(Condition $condition, Fields $fields = null)
    {
        $result = $this->getAll(0, 1, null, null, $condition, $fields);

        return current($result);
    }

    public function get($id, Fields $fields = null)
    {
        $condition = new Condition(array($this->getPrimaryKey(), '=', $id));

        return $this->getOneBy($condition, $fields);
    }

    public function getCount(Condition $condition = null)
    {
        $builder = $this->connection->createQueryBuilder()
            ->select($this->connection->getDatabasePlatform()->getCountExpression($this->getPrimaryKey()))
            ->from($this->getName(), null);

        if ($condition !== null && $condition->hasCondition()) {
            $builder->where($condition->getExpression($this->connection->getDatabasePlatform()));

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

    public function getRecord()
    {
        $supported = $this->getSupportedFields();
        $fields    = array_combine($supported, array_fill(0, count($supported), null));

        return new Record('record', $fields);
    }

    /**
     * Returns an array of fields wich can not be used from the handler even if
     * the fields are available
     *
     * @deprecated
     * @return array
     */
    public function getRestrictedFields()
    {
        return $this->restrictedFields;
    }

    /**
     * Sets the restricted fields. Use the fields parameter to restrict the 
     * result set
     *
     * @deprecated
     * @param array $restrictedFields
     */
    public function setRestrictedFields(array $restrictedFields)
    {
        $this->restrictedFields = $restrictedFields;
    }

    /**
     * Magic method to make conditional selection
     *
     * @param string $method
     * @param string $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (substr($method, 0, 8) == 'getOneBy') {
            $column = lcfirst(substr($method, 8));
            $value  = isset($arguments[0]) ? $arguments[0] : null;
            $field  = isset($arguments[1]) ? $arguments[1] : null;

            if (!empty($value)) {
                $condition = new Condition(array($column, '=', $value));
            } else {
                throw new InvalidArgumentException('Value required');
            }

            return $this->getOneBy($condition, $field);
        } elseif (substr($method, 0, 5) == 'getBy') {
            $column = lcfirst(substr($method, 5));
            $value  = isset($arguments[0]) ? $arguments[0] : null;
            $field  = isset($arguments[1]) ? $arguments[1] : null;

            if (!empty($value)) {
                $condition = new Condition(array($column, '=', $value));
            } else {
                throw new InvalidArgumentException('Value required');
            }

            return $this->getBy($condition, $field);
        } else {
            throw new BadMethodCallException('Undefined method ' . $method);
        }
    }

    /**
     * Builds the SQL query and returns the sql statment and parameters. Can be
     * override to provide a more complex query
     *
     * @param string $table
     * @param array $fields
     * @param integer $startIndex
     * @param integer $count
     * @param string $sortBy
     * @param string $sortOrder
     * @param \PSX\Sql\Condition $condition
     */
    protected function getQuery($table, array $fields, $startIndex, $count, $sortBy, $sortOrder, Condition $condition = null)
    {
        $builder = $this->connection->createQueryBuilder()
            ->select($fields)
            ->from($table, null)
            ->orderBy($sortBy, $sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC')
            ->setFirstResult($startIndex)
            ->setMaxResults($count);

        if ($condition !== null && $condition->hasCondition()) {
            $builder->where($condition->getExpression($this->connection->getDatabasePlatform()));

            $values = $condition->getValues();
            foreach ($values as $key => $value) {
                $builder->setParameter($key, $value);
            }
        }

        return [$builder->getSQL(), $builder->getParameters()];
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
}

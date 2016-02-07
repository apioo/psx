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
use InvalidArgumentException;
use PSX\Sql\Table\ReaderInterface;

/**
 * TableManager
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableManager implements TableManagerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \PSX\Sql\Table\ReaderInterface
     */
    protected $reader;

    protected $_container;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param \PSX\Sql\Table\ReaderInterface $reader
     */
    public function __construct(Connection $connection, ReaderInterface $reader = null)
    {
        $this->connection = $connection;
        $this->reader     = $reader;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $tableName
     * @return \PSX\Sql\TableInterface
     */
    public function getTable($tableName)
    {
        if (isset($this->_container[$tableName])) {
            return $this->_container[$tableName];
        } else {
            if ($this->reader === null) {
                // we assume that $tableName is an class name of an
                // TableInterface implementation
                if (class_exists($tableName)) {
                    $this->_container[$tableName] = new $tableName($this->connection);
                } else {
                    throw new InvalidArgumentException('Table must be a class implementing the PSX\Sql\TableInterface');
                }
            } else {
                $definition = $this->reader->getTableDefinition($tableName);

                $this->_container[$tableName] = new Table($this->connection,
                    $definition->getName(),
                    $definition->getColumns());
            }
        }

        return $this->_container[$tableName];
    }
}

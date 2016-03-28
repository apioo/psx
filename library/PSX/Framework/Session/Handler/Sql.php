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

namespace PSX\Framework\Session\Handler;

use Doctrine\DBAL\Connection;
use PSX\DateTime\DateTime;
use PSX\Sql\Table\ColumnAllocation;
use SessionHandlerInterface;

/**
 * Sql
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Sql implements SessionHandlerInterface
{
    const COLUMN_ID      = 0x1;
    const COLUMN_CONTENT = 0x2;
    const COLUMN_DATE    = 0x3;

    protected $connection;
    protected $tableName;
    protected $allocation;

    public function __construct(Connection $connection, $tableName, ColumnAllocation $allocation)
    {
        $this->connection = $connection;
        $this->tableName  = $tableName;
        $this->allocation = $allocation;
    }

    public function open($path, $name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $builder = $this->connection->createQueryBuilder()
            ->select($this->allocation->get(self::COLUMN_CONTENT))
            ->from($this->tableName)
            ->where($this->allocation->get(self::COLUMN_ID) . ' = :id');

        return $this->connection->fetchColumn($builder->getSQL(), array('id' => $id));
    }

    public function write($id, $data)
    {
        $columnId      = $this->allocation->get(self::COLUMN_ID);
        $columnContent = $this->allocation->get(self::COLUMN_CONTENT);
        $columnDate    = $this->allocation->get(self::COLUMN_DATE);

        $this->connection->insert($this->tableName, array(
            $columnId      => $id,
            $columnContent => $data,
            $columnDate    => date(DateTime::SQL),
        ));
    }

    public function destroy($id)
    {
        $this->connection->delete($this->tableName, array(
            $this->allocation->get(self::COLUMN_ID) => $id,
        ));
    }

    public function gc($maxTime)
    {
        $dateAdd = $this->connection->getDatabasePlatform()->getDateAddSecondsExpression($this->allocation->get(self::COLUMN_DATE), (int) $maxTime);
        $now     = $this->connection->getDatabasePlatform()->getNowExpression();
        $builder = $this->connection->createQueryBuilder()
            ->delete($this->tableName)
            ->where($dateAdd . ' < ' . $now);

        $this->connection->executeUpdate($builder->getSQL(), array('maxTime' => $maxTime));

        return true;
    }
}

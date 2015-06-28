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

namespace PSX\Cache\Handler;

use Doctrine\DBAL\Connection;
use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;
use PSX\DateTime;
use PSX\Sql\Table\ColumnAllocation;

/**
 * This handler stores cache entries in an simple sql table. The table must have
 * the following structure
 * <code>
 * CREATE TABLE `psx_cache` (
 *   `id` varchar(32) NOT NULL,
 *   `content` blob NOT NULL,
 *   `date` datetime DEFAULT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Sql implements HandlerInterface
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

	public function load($key)
	{
		$columnId      = $this->allocation->get(self::COLUMN_ID);
		$columnContent = $this->allocation->get(self::COLUMN_CONTENT);
		$columnDate    = $this->allocation->get(self::COLUMN_DATE);

		$builder = $this->connection->createQueryBuilder();
		$builder = $builder->select(array($columnContent, $columnDate))
			->from($this->tableName)
			->where($builder->expr()->eq($columnId, ':id'))
			->andWhere($builder->expr()->orX(
				$builder->expr()->isNull($columnDate),
				$builder->expr()->gte($columnDate, ':now')
			));

		$row = $this->connection->fetchAssoc($builder->getSQL(), array(
			'id'  => $key,
			'now' => date(DateTime::SQL))
		);

		if(!empty($row))
		{
			$value = $row[$columnContent];
			$ttl   = !empty($row[$columnDate]) ? new \DateTime($row[$columnDate]) : null;

			return new Item($key, unserialize($value), true, $ttl);
		}
		else
		{
			return new Item($key, null, false);
		}
	}

	public function write(Item $item)
	{
		$columnId      = $this->allocation->get(self::COLUMN_ID);
		$columnContent = $this->allocation->get(self::COLUMN_CONTENT);
		$columnDate    = $this->allocation->get(self::COLUMN_DATE);

		$this->connection->insert($this->tableName, array(
			$columnId      => $item->getKey(),
			$columnContent => serialize($item->get()),
			$columnDate    => $item->hasExpiration() ? date(DateTime::SQL) : null,
		));
	}

	public function remove($key)
	{
		$columnId = $this->allocation->get(self::COLUMN_ID);

		$this->connection->delete($this->tableName, array(
			$columnId => $key
		));
	}

	public function removeAll()
	{
		$builder = $this->connection->createQueryBuilder()
			->delete($this->tableName);

		$this->connection->executeUpdate($builder->getSQL());

		return true;
	}
}


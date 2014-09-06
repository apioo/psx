<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Cache\Handler;

use Doctrine\DBAL\Connection;
use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;
use PSX\DateTime;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\ColumnAllocation;
use UnexpectedValueException;

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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		$columnContent = $this->allocation->get(self::COLUMN_CONTENT);
		$columnDate    = $this->allocation->get(self::COLUMN_DATE);

		$condition = new Condition();
		$condition->add($this->allocation->get(self::COLUMN_ID), '=', $key, 'AND');
		$condition->add($this->allocation->get(self::COLUMN_DATE), 'IS', 'NULL', 'OR', Condition::TYPE_RAW);
		$condition->add($this->allocation->get(self::COLUMN_DATE), '>=', date(DateTime::SQL));

		$sql = 'SELECT ' . $columnContent . ', ' . $columnDate . ' FROM ' . $this->tableName . ' ' . $condition->getStatment();
		$row = $this->connection->fetchAssoc($sql, $condition->getValues());

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
		$this->connection->query('DELETE FROM ' . $this->tableName . ' WHERE 1');

		return true;
	}
}


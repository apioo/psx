<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Session\Handler;

use Doctrine\DBAL\Connection;
use PSX\DateTime;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\ColumnAllocation;
use SessionHandlerInterface;

/**
 * Sql
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
		$columnId      = $this->allocation->get(self::COLUMN_ID);
		$columnContent = $this->allocation->get(self::COLUMN_CONTENT);

		$sql     = 'SELECT `' . $columnContent . '` FROM `' . $this->tableName . '` WHERE `' . $columnId . '` = :id';
		$content = $this->connection->fetchColumn($sql, array('id' => $id));

		return $content;
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
		$columnDate = $this->allocation->get(self::COLUMN_DATE);

		$maxTime = (int) $maxTime;
		$sql     = 'DELETE FROM `' . $this->tableName . '` WHERE DATE_ADD(`' . $columnDate . '`, INTERVAL :maxTime SECOND) < NOW()';

		$this->connection->executeUpdate($sql, array('maxTime' => $maxTime));

		return true;
	}
}

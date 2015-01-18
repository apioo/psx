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

namespace PSX\Filter;

use Doctrine\DBAL\Connection;
use PSX\FilterAbstract;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;

/**
 * Checks whether the value is in the column
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class InColumn extends FilterAbstract
{
	protected $connection;
	protected $tableName;
	protected $columnName;

	public function __construct(Connection $connection, $tableName, $columnName)
	{
		$this->connection = $connection;
		$this->tableName  = $tableName;
		$this->columnName = $columnName;
	}

	/**
	 * Returns true if value is in the table
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function apply($value)
	{
		$builder = $this->connection->createQueryBuilder()
			->select($this->connection->getDatabasePlatform()->getCountExpression('*'))
			->from($this->tableName, null)
			->where($this->columnName . ' = :value');

		$count = (int) $this->connection->fetchColumn($builder->getSQL(), array(
			'value' => $value
		));

		return $count > 0;
	}

	public function getErrorMessage()
	{
		return '%s is not a valid value';
	}
}

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

namespace PSX\Test;

use InvalidArgumentException;
use PSX\Sql\TableInterface;

/**
 * TableDataSet
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableDataSet extends \PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
	protected $tables;

	public function __construct()
	{
		$this->tables = array();
	}

	public function addTable(TableInterface $table, array $data)
	{
		$this->tables[$table->getName()] = new Table($table, $data);
	}

	protected function createIterator($reverse = false)
	{
		return new \PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
	}

	public function getTable($tableName)
	{
		if(!isset($this->tables[$tableName]))
		{
			throw new InvalidArgumentException($tableName . ' is not a table in the current database');
		}

		return $this->tables[$tableName];
	}
}

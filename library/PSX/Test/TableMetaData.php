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

namespace PSX\Test;

use PSX\Sql\TableInterface;

/**
 * TableMetaData
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableMetaData implements \PHPUnit_Extensions_Database_DataSet_ITableMetaData
{
	private $table;

	public function __construct(TableInterface $table)
	{
		$this->table = $table;
	}

	public function matches(\PHPUnit_Extensions_Database_DataSet_ITableMetaData $other)
	{
		return $this->getTableName() == $other->getTableName();
	}

	public function getColumns()
	{
		return array_keys($this->table->getColumns());
	}

	public function getPrimaryKeys() 
	{
		return array($this->table->getPrimaryKey());
	}

	public function getTableName() 
	{
		return $this->table->getName();
	}
}


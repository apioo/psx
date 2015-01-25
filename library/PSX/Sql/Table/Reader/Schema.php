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

namespace PSX\Sql\Table\Reader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use PSX\Sql;
use PSX\Sql\SerializeTrait;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;

/**
 * Schema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Schema implements ReaderInterface
{
	protected $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	public function getTableDefinition($tableName)
	{
		$sm = $this->connection->getSchemaManager();

		// columns
		$table   = $sm->listTableDetails($tableName);
		$columns = array();

		foreach($table->getColumns() as $column)
		{
			$columns[$column->getName()] = $this->getType($column);
		}

		// set primary key
		$pk        = $table->getPrimaryKey();
		$pkColumns = $pk->getColumns();

		if(count($pkColumns) == 1 && $pk->isPrimary())
		{
			$pkColumn = $pkColumns[0];

			if(isset($columns[$pkColumn]))
			{
				$columns[$pkColumn] = $columns[$pkColumn] | TableInterface::PRIMARY_KEY;
			}
		}

		// foreign keys
		$connections = array();

		foreach($table->getForeignKeys() as $fk)
		{
			$columnNames = $fk->localColumnNames();

			if(count($columnNames) == 1)
			{
				$connections[$columnNames[0]] = $fk->getForeignTableName();
			}
		}

		return new Definition($tableName, $columns, $connections);
	}

	protected function getType(Column $column)
	{
		$type = 0;

		if($column->getLength() > 0)
		{
			$type+= $column->getLength();
		}

		$type = $type | SerializeTrait::getTypeByDoctrineType($column->getType());

		if(!$column->getNotnull())
		{
			$type = $type | TableInterface::IS_NULL;
		}

		if($column->getAutoincrement())
		{
			$type = $type | TableInterface::AUTO_INCREMENT;
		}

		return $type;
	}
}

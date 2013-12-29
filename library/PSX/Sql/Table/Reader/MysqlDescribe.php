<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Sql;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;

/**
 * MysqlDescribe
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MysqlDescribe implements ReaderInterface
{
	protected $sql;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;
	}

	public function getTableDefinition($tableName)
	{
		$columns = array();
		$result  = $this->sql->getAll('DESCRIBE `' . $tableName . '`');

		foreach($result as $row)
		{
			$columns[$row['Field']] = $this->getType($row);
		}

		// get foreign keys
		$conns = array();
		$query = <<<SQL
SELECT
	`COLUMN_NAME`,
	`REFERENCED_TABLE_NAME`
FROM
	information_schema.KEY_COLUMN_USAGE
WHERE
	TABLE_NAME = ?
AND
	REFERENCED_TABLE_NAME IS NOT NULL;
SQL;

		$result = $this->sql->getAll($query, array($tableName));

		foreach($result as $row)
		{
			$conns[$row['COLUMN_NAME']] = $row['REFERENCED_TABLE_NAME'];
		}

		return new Definition($tableName, $columns, $conns);
	}

	protected function getType($row)
	{
		$type = 0;
		$pos  = strpos($row['Type'], '(');

		if($pos !== false)
		{
			$len  = (integer) substr($row['Type'], $pos + 1);
			$name = substr($row['Type'], 0, $pos);

			$type+= $len;
		}
		else
		{
			$name = $row['Type'];
		}

		$const = 'TYPE_' . strtoupper($name);
		$type  = $type | constant('PSX\Sql\TableInterface::' . $const);

		if($row['Null'] == 'YES')
		{
			$type = $type | TableInterface::IS_NULL;
		}

		if($row['Key'] == 'PRI')
		{
			$type = $type | TableInterface::PRIMARY_KEY;
		}

		if($row['Extra'] == 'auto_increment')
		{
			$type = $type | TableInterface::AUTO_INCREMENT;
		}

		return $type;
	}
}

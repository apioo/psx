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

namespace PSX\Sql;

use PSX\Exception;

/**
 * Join
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Join
{
	const INNER = 0x1;
	const LEFT  = 0x2;
	const RIGHT = 0x3;

	private $type;
	private $table;
	private $cardinality;
	private $foreignKey;
	private $alias;

	public function __construct($type, TableInterface $table, $cardinality = '1:n', $foreignKey = null)
	{
		$this->setType($type);
		$this->setTable($table);
		$this->setCardinality($cardinality);
		$this->setForeignKey($foreignKey);

		$this->alias = $table->getLastSelect()->getPrefix();
	}

	public function setType($type)
	{
		$this->type = $type === self::RIGHT ? 'RIGHT' : ($type === self::LEFT ? 'LEFT' : 'INNER');
	}

	public function setTable(TableInterface $table)
	{
		$this->table = $table;
	}

	public function setCardinality($cardinality)
	{
		$cardi = explode(':', $cardinality);

		if(count($cardi) == 2)
		{
			list($l, $r) = $cardi;

			if(($l == '1' || $l == 'n') && ($r == '1' || $r == 'n'))
			{
				$this->cardinality = array($l, $r);

				return true;
			}
		}

		throw new Exception('Invalid cardinality');
	}

	public function setForeignKey($foreignKey)
	{
		$this->foreignKey = $foreignKey;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getCardinality()
	{
		return $this->cardinality;
	}

	public function getForeignKey()
	{
		return $this->foreignKey;
	}

	public function getAlias()
	{
		return $this->alias;
	}
}

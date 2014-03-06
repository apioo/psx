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

namespace PSX\Sql;

use PSX\Data\RecordInterface;
use PSX\Exception;
use PSX\Sql;
use PSX\Sql\Table\Select;
use ReflectionClass;

/**
 * Table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Table extends TableAbstract
{
	protected $name;
	protected $columns = array();
	protected $connections = array();

	public function __construct(Sql $sql, $name, array $columns, array $connections = array())
	{
		parent::__construct($sql);

		$this->name        = $name;
		$this->columns     = $columns;
		$this->connections = $connections;
	}

	public function getConnections()
	{
		return $this->connections;
	}

	public function addConnection($column, $table)
	{
		if($this->hasColumn($column))
		{
			$this->connections[$column] = $table;
		}
		else
		{
			throw new Exception('Invalid column');
		}
	}

	public function getName()
	{
		return $this->name;
	}

	public function getColumns()
	{
		return $this->columns;
	}
}

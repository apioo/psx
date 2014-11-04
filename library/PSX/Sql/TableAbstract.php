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

use PSX\Handler\Impl\TableHandlerAbstract;
use PSX\Handler\Impl\Table\Mapping;
use PSX\Handler\MappingAbstract;
use PSX\Sql\Table\Select;

/**
 * TableAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TableAbstract extends TableHandlerAbstract implements TableInterface
{
	protected $select;

	public function getConnections()
	{
		return array();
	}

	public function getDisplayName()
	{
		$name = $this->getName();
		$pos  = strrpos($name, '_');

		return $pos !== false ? substr($name, strrpos($name, '_') + 1) : $name;
	}

	public function getPrimaryKey()
	{
		return $this->getFirstColumnWithAttr(self::PRIMARY_KEY);
	}

	public function getFirstColumnWithAttr($searchAttr)
	{
		$columns = $this->getColumns();

		foreach($columns as $column => $attr)
		{
			if($attr & $searchAttr)
			{
				return $column;
			}
		}

		return null;
	}

	public function getFirstColumnWithType($searchType)
	{
		$columns = $this->getColumns();

		foreach($columns as $column => $attr)
		{
			if(((($attr >> 20) & 0xFF) << 20) === $searchType)
			{
				return $column;
			}
		}

		return null;
	}

	public function getValidColumns(array $columns)
	{
		return array_intersect($columns, array_keys($this->getColumns()));
	}

	public function hasColumn($column)
	{
		$columns = array_keys($this->getColumns());

		return isset($columns[$column]);
	}

	public function select(array $columns = array(), $prefix = null)
	{
		$this->select = new Select($this->connection, $this, $prefix);

		if(in_array('*', $columns))
		{
			$this->select->select(array_keys($this->getColumns()));
		}
		else
		{
			$this->select->select($columns);
		}

		return $this->select;
	}

	public function getLastSelect()
	{
		return $this->select;
	}

	public function getMapping()
	{
		$fields  = array();
		$columns = $this->getColumns();

		foreach($columns as $name => $type)
		{
			$fields[$name] = $this->convertColumnTypes($type);
		}

		return new Mapping($this->getName(), $fields, $this->getConnections());
	}

	protected function convertColumnTypes($type)
	{
		$value = 0;

		if($type & self::PRIMARY_KEY)
		{
			$value|= MappingAbstract::ID_PROPERTY;
		}

		$type = (($type >> 20) & 0xFF) << 20;

		switch($type)
		{
			case TableInterface::TYPE_TINYINT:
			case TableInterface::TYPE_SMALLINT:
			case TableInterface::TYPE_MEDIUMINT:
			case TableInterface::TYPE_INT:
			case TableInterface::TYPE_BIGINT:
			case TableInterface::TYPE_BIT:
			case TableInterface::TYPE_SERIAL:
				$value|= MappingAbstract::TYPE_INTEGER;
				break;

			case TableInterface::TYPE_DECIMAL:
			case TableInterface::TYPE_FLOAT:
			case TableInterface::TYPE_DOUBLE:
			case TableInterface::TYPE_REAL:
				$value|= MappingAbstract::TYPE_FLOAT;
				break;

			case TableInterface::TYPE_BOOLEAN:
				$value|= MappingAbstract::TYPE_BOOLEAN;
				break;

			case TableInterface::TYPE_DATE:
			case TableInterface::TYPE_DATETIME:
				$value|= MappingAbstract::TYPE_DATETIME;
				break;

			default:
				$value|= MappingAbstract::TYPE_STRING;
				break;
		}

		return $value;
	}
}

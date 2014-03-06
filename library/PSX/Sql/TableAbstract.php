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
 * TableAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TableAbstract implements TableInterface
{
	protected $sql;
	protected $select;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;
	}

	public function getSql()
	{
		return $this->sql;
	}

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
		$this->select = new Select($this, $prefix);

		if(in_array('*', $columns))
		{
			$this->select->setColumns(array_keys($this->getColumns()));
		}
		else
		{
			$this->select->setColumns($columns);
		}

		return $this->select;
	}

	public function getLastSelect()
	{
		return $this->select;
	}

	public function getRecord($id = null, $class = null, array $args = array())
	{
		if($class === null)
		{
			$class = 'stdClass';
		}

		if($id !== null)
		{
			$fields = implode(', ', array_map('\PSX\Sql::helpQuote', array_keys($this->getColumns())));
			$sql    = 'SELECT ' . $fields . ' FROM `' . $this->getName() . '` WHERE `' . $this->getPrimaryKey() . '` = ?';
			$record = $this->sql->getRow($sql, array($id), Sql::FETCH_OBJECT, $class, $args);

			if(empty($record))
			{
				throw new Exception('Invalid record id');
			}
		}
		else
		{
			$ref    = new ReflectionClass($class);
			$record = $ref->newInstanceArgs($args);
		}

		return $record;
	}

	public function getAll(array $fields, Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, Sql::SELECT_ALL, $sortBy, $sortOrder, $startIndex, $count);
	}

	public function getRow(array $fields, Condition $condition = null, $sortBy = null, $sortOrder = 0)
	{
		$fields = $this->getValidColumns($fields);

		return $this->sql->select($this->getName(), $fields, $condition, Sql::SELECT_ROW, $sortBy, $sortOrder);
	}

	public function getCol($field, Condition $condition = null, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32)
	{
		$fields = $this->getValidColumns(array($field));

		return $this->sql->select($this->getName(), $fields, $condition, Sql::SELECT_COL, $sortBy, $sortOrder, $startIndex, $count);
	}

	public function getField($field, Condition $condition = null, $sortBy = null, $sortOrder = 0)
	{
		$fields = $this->getValidColumns(array($field));

		return $this->sql->select($this->getName(), $fields, $condition, Sql::SELECT_FIELD, $sortBy, $sortOrder);
	}

	public function count(Condition $condition = null)
	{
		return $this->sql->count($this->getName(), $condition);
	}

	public function insert($params, $modifier = 0)
	{
		if(is_array($params))
		{
		}
		else if($params instanceof RecordInterface)
		{
			$params = $params->getRecordInfo()->getData();
		}
		else
		{
			throw new Exception('Params must be either an array or instance of PSX\Data\RecordInterface');
		}

		$fields = array_intersect_key($params, $this->getColumns());

		if(empty($fields))
		{
			throw new Exception('No valid field set');
		}

		return $this->sql->insert($this->getName(), $fields, $modifier);
	}

	public function update($params, Condition $condition = null, $modifier = 0)
	{
		if(is_array($params))
		{
		}
		else if($params instanceof RecordInterface)
		{
			$params = $params->getRecordInfo()->getData();
		}
		else
		{
			throw new Exception('Params must be either an array or instance of PSX\Data\RecordInterface');
		}

		$fields = array_intersect_key($params, $this->getColumns());

		if(!empty($fields))
		{
			if($condition === null)
			{
				$pk = $this->getPrimaryKey();

				if(isset($fields[$pk]))
				{
					$condition = new Condition(array($pk, '=', $fields[$pk]));
				}
				else
				{
					throw new Exception('No primary key set');
				}
			}
		}
		else
		{
			throw new Exception('No valid field set');
		}

		return $this->sql->update($this->getName(), $fields, $condition, $modifier);
	}

	public function replace($params, $modifier = 0)
	{
		if(is_array($params))
		{
		}
		else if($params instanceof RecordInterface)
		{
			$params = $params->getRecordInfo()->getData();
		}
		else
		{
			throw new Exception('Params must be either an array or instance of PSX\Data\RecordInterface');
		}

		$fields = array_intersect_key($params, $this->getColumns());

		if(empty($fields))
		{
			throw new Exception('No valid field set');
		}

		return $this->sql->replace($this->getName(), $fields, $modifier);
	}

	public function delete($params, $modifier = 0)
	{
		$condition = null;

		if(is_array($params))
		{
		}
		else if($params instanceof RecordInterface)
		{
			$params = $params->getRecordInfo()->getData();
		}
		else if($params instanceof Condition)
		{
			$condition = $params;
		}
		else
		{
			throw new Exception('Params must be either an array or instance of PSX\Data\RecordInterface');
		}

		if($condition === null)
		{
			$fields = array_intersect_key($params, $this->getColumns());

			if(!empty($fields))
			{
				$pk = $this->getPrimaryKey();

				if(isset($fields[$pk]))
				{
					$condition = new Condition(array($pk, '=', $fields[$pk]));
				}
				else
				{
					throw new Exception('No primary key set');
				}
			}
			else
			{
				throw new Exception('No valid field set');
			}
		}

		return $this->sql->delete($this->getName(), $condition, $modifier);
	}
}

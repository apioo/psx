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

namespace PSX\Data;

use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\ResultSet;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;

/**
 * Default abstract class wich implements all necessary methods using an 
 * TableInterface. The TableInterface only simplyfies creating sql queries you
 * could also write an handler wich uses simple sql queries.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class HandlerAbstract implements HandlerInterface
{
	protected $table;

	protected $_select;

	public function __construct(TableInterface $table)
	{
		$this->table = $table;
	}

	public function getAll(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null, $mode = 0, $class = null, array $args = array())
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy               : $this->table->getPrimaryKey();
		$sortOrder  = $sortOrder  !== null ? (integer) $sortOrder  : Sql::SORT_DESC;

		$select = $this->getSelect();
		$fields = array_intersect($fields, $select->getSupportedFields());

		if(!empty($fields))
		{
			$select->setColumns($fields);
		}
		else
		{
			$select->setColumns(array_keys($select->getAllColumns()));
		}

		$select->orderBy($sortBy, $sortOrder)
			->limit($startIndex, $count);

		if($con !== null && $con->hasCondition())
		{
			$values = $con->toArray();

			foreach($values as $row)
			{
				$select->where($row[Condition::COLUMN], 
					$row[Condition::OPERATOR], 
					$row[Condition::VALUE], 
					$row[Condition::CONJUNCTION], 
					$row[Condition::TYPE]);
			}
		}

		if($mode == Sql::FETCH_OBJECT && $class === null)
		{
			$class = $this->getClassName();
		}

		if($mode == Sql::FETCH_OBJECT && empty($args))
		{
			$args = $this->getClassArgs();
		}

		return $select->getAll($mode, $class, $args);
	}

	public function getBy(Condition $con, array $fields = array(), $mode = 0, $class = null, array $args = array())
	{
		return $this->getAll($fields, null, null, null, null, $con, $mode, $class, $args);
	}

	public function getOneBy(Condition $con, array $fields = array(), $mode = 0, $class = null, array $args = array())
	{
		$select = $this->getSelect();
		$fields = array_intersect($fields, $select->getSupportedFields());

		if(!empty($fields))
		{
			$select->setColumns($fields);
		}
		else
		{
			$select->setColumns(array_keys($select->getAllColumns()));
		}

		if($mode == Sql::FETCH_OBJECT && $class === null)
		{
			$class = $this->getClassName();
		}

		if($mode == Sql::FETCH_OBJECT && empty($args))
		{
			$args = $this->getClassArgs();
		}

		if($con->hasCondition())
		{
			$values = $con->toArray();

			foreach($values as $row)
			{
				$select->where($row[Condition::COLUMN], 
					$row[Condition::OPERATOR], 
					$row[Condition::VALUE], 
					$row[Condition::CONJUNCTION], 
					$row[Condition::TYPE]);
			}
		}

		return $select->getRow($mode, $class, $args);
	}

	public function get($id, array $fields = array(), $mode = 0, $class = null, array $args = array())
	{
		$select = $this->getSelect();
		$fields = array_intersect($fields, $select->getSupportedFields());

		if(!empty($fields))
		{
			$select->setColumns($fields);
		}

		if($mode == Sql::FETCH_OBJECT && $class === null)
		{
			$class = $this->getClassName();
		}

		if($mode == Sql::FETCH_OBJECT && empty($args))
		{
			$args = $this->getClassArgs();
		}

		$select->where($select->getTable()->getPrimaryKey(), '=', $id);

		return $select->getRow($mode, $class, $args);
	}

	public function getResultSet(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null, $mode = 0, $class = null, array $args = array())
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortOrder  = $sortOrder  !== null ? (strcasecmp($sortOrder, 'ascending') == 0 ? Sql::SORT_ASC : Sql::SORT_DESC) : null;

		$totalResults = $this->getCount($con);
		$entries      = $this->getAll($fields, $startIndex, $count, $sortBy, $sortOrder, $con, $mode, $class, $args);
		$resultSet    = new ResultSet($totalResults, $startIndex, $count, $entries);

		return $resultSet;
	}

	public function getSupportedFields()
	{
		return $this->getSelect()->getSupportedFields();
	}

	public function getCount(Condition $con = null)
	{
		$select = $this->getSelect();

		if($con !== null && $con->hasCondition())
		{
			$values = $con->toArray();

			foreach($values as $row)
			{
				$select->where($row[Condition::COLUMN], 
					$row[Condition::OPERATOR], 
					$row[Condition::VALUE], 
					$row[Condition::CONJUNCTION], 
					$row[Condition::TYPE]);
			}
		}

		return $select->getTotalResults();
	}

	public function getRecord($id = null, $class = null, array $args = null)
	{
		if($class === null)
		{
			$class = $this->getClassName();
		}

		if($args === null)
		{
			$args = $this->getClassArgs();
		}

		return $this->table->getRecord($id, $class, $args);
	}

	public function getClassName()
	{
		return 'stdClass';
	}

	public function create(RecordInterface $record)
	{
		$this->table->insert($record);
	}

	public function update(RecordInterface $record)
	{
		$this->table->update($record);
	}

	public function delete(RecordInterface $record)
	{
		$this->table->delete($record);
	}

	public function __call($method, $arguments)
	{
		if(substr($method, 0, 8) == 'getOneBy')
		{
			$column = lcfirst(substr($method, 8));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : array();
			$mode   = isset($arguments[2]) ? $arguments[2] : 0;
			$class  = isset($arguments[3]) ? $arguments[3] : null;
			$args   = isset($arguments[4]) ? $arguments[4] : array();

			if(!empty($value))
			{
				$con = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getOneBy($con, $fields, $mode, $class, $args);
		}
		else if(substr($method, 0, 5) == 'getBy')
		{
			$column = lcfirst(substr($method, 5));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : array();
			$mode   = isset($arguments[2]) ? $arguments[2] : 0;
			$class  = isset($arguments[3]) ? $arguments[3] : null;
			$args   = isset($arguments[4]) ? $arguments[4] : array();

			if(!empty($value))
			{
				$con = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getBy($con, $fields, $mode, $class, $args);
		}
		else
		{
			throw new BadMethodCallException('Undefined method');
		}
	}

	protected function getSelect()
	{
		if($this->_select === null)
		{
			$this->_select = $this->getDefaultSelect();
		}

		$select = clone $this->_select;
		$select->getCondition()->removeAll();

		return $select;
	}

	protected function getDefaultSelect()
	{
		return $this->table
			->select(array('*'));
	}

	protected function getClassArgs()
	{
		return array();
	}
}

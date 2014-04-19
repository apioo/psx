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

namespace PSX\Sql\Table;

use PSX\DateTime;
use PSX\Data\ResultSet;
use PSX\Exception;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\Join;
use PSX\Sql\TableInterface;

/**
 * Select
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Select implements SelectInterface
{
	protected $table;
	protected $sql;
	protected $condition;

	protected $joins   = array();
	protected $columns = array();
	protected $prefix;

	protected $selectedColumns  = array();
	protected $selfColumns      = array();
	protected $availableColumns = array();

	protected $start;
	protected $count;

	protected $orderBy = array();
	protected $groupBy = array();

	public function __construct(TableInterface $table, $prefix = null)
	{
		$this->table     = $table;
		$this->sql       = $table->getSql();
		$this->condition = new Condition();

		$this->setPrefix($prefix);
	}

	public function join($type, $table, $cardinality = 'n:1', $foreignKey = null)
	{
		if($table instanceof TableInterface)
		{
		}
		else if($table instanceof SelectInterface)
		{
			$this->selectedColumns = array_merge($this->selectedColumns, $table->getAllSelectedColumns());

			$table = $table->getTable();
		}
		else
		{
			throw new Exception('Invalid table must be instanceof PSX\Sql\TableInterface or PSX\Sql\Table\SelectInterface');
		}

		if($table->getLastSelect() === null)
		{
			throw new Exception('Nothing is selected on table ' . $table->getName());
		}

		$this->joins[] = new Join($type, $table, $cardinality, $foreignKey);

		$this->condition->merge($table->getLastSelect()->getCondition());

		$this->availableColumns = array_merge($this->availableColumns, $table->getLastSelect()->getAllColumns());

		return $this;
	}

	public function where($column, $operator, $value, $conjunction = 'AND')
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->condition->add($this->availableColumns[$column], $operator, $value, $conjunction);

		return $this;
	}

	public function groupBy($column)
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->groupBy[] = $this->availableColumns[$column];

		return $this;
	}

	public function orderBy($column, $sort = 0x1)
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->orderBy[] = array($column, $sort === Sql::SORT_ASC ? 'ASC' : 'DESC');

		return $this;
	}

	public function limit($start, $count = null)
	{
		if($count === null)
		{
			$this->start = 0;
			$this->count = (int) $start;
		}
		else
		{
			$this->start = (int) $start;
			$this->count = (int) $count;
		}

		return $this;
	}

	public function getAll($mode = 0, $class = null, array $args = array())
	{
		if($mode == Sql::FETCH_OBJECT && $class === null)
		{
			$class = 'stdClass';
		}

		return $this->sql->getAll($this->buildQuery(), $this->condition->getValues(), $mode, $class, $args);
	}

	public function getRow($mode = 0, $class = null, array $args = array())
	{
		if($mode == Sql::FETCH_OBJECT && $class === null)
		{
			$class = 'stdClass';
		}

		$this->limit(1);

		return $this->sql->getRow($this->buildQuery(), $this->condition->getValues(), $mode, $class, $args);
	}

	public function getCol()
	{
		return $this->sql->getCol($this->buildQuery(), $this->condition->getValues());
	}

	public function getField()
	{
		$this->limit(1);

		return $this->sql->getField($this->buildQuery(), $this->condition->getValues());
	}

	public function getTotalResults()
	{
		return (int) $this->sql->getField($this->buildCountQuery(), $this->condition->getValues());
	}

	public function getSupportedFields()
	{
		return array_keys($this->availableColumns);
	}

	public function setColumns(array $columns)
	{
		$this->columns         = $columns;
		$this->selectedColumns = $this->getSelectedColumns();

		return $this;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function setSelectedColumns(array $columns)
	{
		$selectedColumns = array();

		foreach($columns as $column)
		{
			if(isset($this->availableColumns[$column]))
			{
				$selectedColumns[$column] = $this->availableColumns[$column];
			}
		}

		$this->selectedColumns = $selectedColumns;
	}

	/**
	 * Returns the underlying table
	 *
	 * @return PSX\Sql\TableInterface
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Returns the sql connection from the table
	 *
	 * @return PSX\Sql
	 */
	public function getSql()
	{
		return $this->sql;
	}

	/**
	 * Returns the condition
	 *
	 * @return PSX\Sql\Condition
	 */
	public function getCondition()
	{
		return $this->condition;
	}

	/**
	 * Returns all available joins
	 *
	 * @return PSX\Sql\Join
	 */
	public function getJoins()
	{
		return $this->joins;
	}

	/**
	 * Returns the prefix for this select
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * Set the prefix
	 *
	 * @param string $prefix
	 */
	public function setPrefix($prefix)
	{
		$this->prefix           = $prefix === null ? '__self' : $prefix;
		$this->selfColumns      = $this->getSelfColumns();
		$this->availableColumns = $this->selfColumns;

		return $this;
	}

	/**
	 * Returns all available columns
	 *
	 * @return array
	 */
	public function getAllColumns()
	{
		return $this->availableColumns;
	}

	/**
	 * Returns all selected columns
	 *
	 * @return array
	 */
	public function getAllSelectedColumns()
	{
		return $this->selectedColumns;
	}

	/**
	 * Returns the join sql query. Is internally used to build the complete sql
	 * query
	 *
	 * @return string
	 */
	public function buildJoins()
	{
		$sql = '';

		foreach($this->joins as $join)
		{
			$fk    = $join->getForeignKey();
			$table = $join->getTable();
			$cardi = $join->getCardinality();

			$sql.= $join->getType() . ' JOIN `' . $table->getName() . '` AS `' . $join->getAlias() . '` ON ';

			if($cardi[0] == '1')
			{
				$sql.= '`' . $this->prefix . '`.`' . $this->table->getPrimaryKey() . '` = ';
			}
			else if($cardi[0] == 'n')
			{
				$fk  = $fk === null ? $this->getForeignKeyByTable($this->table->getConnections(), $table->getName()) : $fk;
				$sql.= '`' . $this->prefix . '`.`' . $fk . '` = ';
			}

			if($cardi[1] == '1')
			{
				$sql.= '`' . $join->getAlias() . '`.`' . $table->getPrimaryKey() . '`';
			}
			else if($cardi[1] == 'n')
			{
				$fk  = $fk === null ? $this->getForeignKeyByTable($table->getConnections(), $this->table->getName()) : $fk;
				$sql.= '`' . $join->getAlias() . '`.`' . $fk . '`';
			}

			$sql.= ' ' . $table->getLastSelect()->buildJoins() . ' ';
		}

		return $sql;
	}

	public function __clone()
	{
		$this->condition = clone $this->condition;
	}

	protected function getSelfColumns()
	{
		$columns     = array();
		$selfColumns = $this->table->getColumns();

		foreach($selfColumns as $column => $attr)
		{
			$key   = $this->prefix !== '__self' ? $this->prefix . ucfirst($column) : $column;
			$value = '`' . $this->prefix . '`.`' . $column . '`';

			$columns[$key] = $value;
		}

		return $columns;
	}

	protected function getSelectedColumns()
	{
		$selectedColumns = array();
		$columns         = $this->table->getColumns();

		foreach($this->columns as $column)
		{
			if($this->prefix !== '__self' && isset($columns[$column]))
			{
				$column = $this->prefix . ucfirst($column);
			}

			if(isset($this->availableColumns[$column]))
			{
				$selectedColumns[$column] = $this->availableColumns[$column];
			}
		}

		return $selectedColumns;
	}

	protected function buildQuery()
	{
		$selectedColumns = $this->getAllSelectedColumns();

		if(empty($selectedColumns))
		{
			throw new Exception('No valid columns selected');
		}

		// select
		$sql = 'SELECT ';
		$i   = 0;
		$len = count($selectedColumns) - 1;

		foreach($selectedColumns as $alias => $column)
		{
			$sql.= $column . ' AS `' . $alias . '`' . ($i < $len ? ',' : '');

			$i++;
		}

		$sql.= ' FROM `' . $this->table->getName() . '` AS `' . $this->prefix . '` ' . $this->buildJoins();

		// where
		if($this->condition->hasCondition())
		{
			$sql.= $this->condition->getStatment();
		}

		// group by
		if(!empty($this->groupBy))
		{
			$sql.= ' GROUP BY ' . implode(', ', $this->groupBy);
		}

		// order
		if(!empty($this->orderBy))
		{
			$len = count($this->orderBy) - 1;
			$sql.= ' ORDER BY';

			foreach($this->orderBy as $key => $orderBy)
			{
				$sql.= $this->availableColumns[$orderBy[0]] . ' ' . $orderBy[1] . ' ' . ($len == $key ? '' : ',');
			}
		}

		// limit
		if($this->start !== null)
		{
			$sql.= ' LIMIT ' . $this->start . ', ' . $this->count;
		}

		return $sql;
	}

	protected function buildCountQuery()
	{
		// select
		$sql = 'SELECT COUNT(*) FROM `' . $this->table->getName() . '` AS `' . $this->prefix . '` ' . $this->buildJoins();

		// condition
		if($this->condition->hasCondition())
		{
			$sql.= $this->condition->getStatment();
		}

		return $sql;
	}

	protected function getForeignKeyByTable(array $connections, $foreignTable)
	{
		foreach($connections as $column => $table)
		{
			if($table == $foreignTable)
			{
				return $column;
			}
		}

		throw new Exception($foreignTable . ' is not connected to ' . $this->table->getName());
	}
}

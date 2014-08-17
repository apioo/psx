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

use BadMethodCallException;
use Doctrine\DBAL\Connection;
use PSX\DateTime;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Handler\HandlerAbstract;
use PSX\Exception;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\Join;
use PSX\Sql\TableInterface;

/**
 * The select represents an view on an table. This view can be used as an 
 * handler. We have two ways of restrict the result set first through the
 * SelectInterface methods where(), orderBy() etc. and through the parameters
 * which gets passed to the getAll() method. The SelectInterface methods have
 * always a higher priority
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Select extends HandlerAbstract implements SelectInterface
{
	protected $connection;
	protected $table;
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

	public function __construct(Connection $connection, TableInterface $table, $prefix = null)
	{
		$this->connection = $connection;
		$this->table      = $table;
		$this->condition  = new Condition();

		$this->setPrefix($prefix);
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getCondition()
	{
		return $this->condition;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function getAllColumns()
	{
		return $this->availableColumns;
	}

	public function getAllSelectedColumns()
	{
		return $this->selectedColumns;
	}

	public function select(array $columns)
	{
		$this->columns         = $columns;
		$this->selectedColumns = $this->getSelectedColumns();

		return $this;
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

		$this->condition->add($this->availableColumns[$column][0], $operator, $value, $conjunction);

		return $this;
	}

	public function groupBy($column)
	{
		if(!isset($this->availableColumns[$column]))
		{
			throw new Exception('Invalid column');
		}

		$this->groupBy[] = $this->availableColumns[$column][0];

		return $this;
	}

	public function orderBy($column, $sort = Sql::SORT_DESC)
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

	public function getAll(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$startIndex = $startIndex !== null ? (int) $startIndex : 0;
		$count      = !empty($count)       ? (int) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy           : $this->table->getPrimaryKey();
		$sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

		$copyCondition = clone $this->condition;
		if($condition !== null)
		{
			$copyCondition->merge($condition);
		}

		return $this->project($this->buildQuery($fields, $startIndex, $count, $sortBy, $sortOrder, $copyCondition), $copyCondition->getValues());
	}

	public function get($id)
	{
		$condition = new Condition(array($this->table->getPrimaryKey(), '=', $id));

		return $this->getOneBy($condition);
	}

	public function getCount(Condition $condition = null)
	{
		$copyCondition = clone $this->condition;
		if($condition !== null)
		{
			$copyCondition->merge($condition);
		}

		return (int) $this->connection->fetchColumn($this->buildCountQuery($copyCondition), $copyCondition->getValues());
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->availableColumns), $this->getRestrictedFields());
	}

	public function create(RecordInterface $record)
	{
		$this->table->create($record);
	}

	public function update(RecordInterface $record)
	{
		$this->table->update($record);
	}

	public function delete(RecordInterface $record)
	{
		$this->table->delete($record);
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

	protected function setPrefix($prefix)
	{
		$this->prefix           = $prefix === null ? '__self' : $prefix;
		$this->selfColumns      = $this->getSelfColumns();
		$this->availableColumns = $this->selfColumns;
	}

	protected function getSelfColumns()
	{
		$columns     = array();
		$selfColumns = $this->table->getColumns();

		foreach($selfColumns as $column => $attr)
		{
			$key   = $this->prefix !== '__self' ? $this->prefix . ucfirst($column) : $column;
			$value = '`' . $this->prefix . '`.`' . $column . '`';

			$columns[$key] = array($value, $attr);
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
				$selectedColumns[$column] = $this->availableColumns[$column][0];
			}
		}

		return $selectedColumns;
	}

	protected function buildQuery(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$selectedColumns = $this->getAllSelectedColumns();

		if($fields !== null)
		{
			$selectedColumns = array_intersect_key($selectedColumns, array_flip($fields));
		}

		$selectedColumns = array_intersect_key($selectedColumns, array_flip($this->getSupportedFields()));

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
		if($condition->hasCondition())
		{
			$sql.= $condition->getStatment();
		}

		// group by
		if(!empty($this->groupBy))
		{
			$sql.= ' GROUP BY ' . implode(', ', $this->groupBy);
		}

		// order
		if($sortBy !== null && $sortOrder !== null)
		{
			$this->orderBy($sortBy, $sortOrder);
		}

		if(!empty($this->orderBy))
		{
			$len = count($this->orderBy) - 1;
			$sql.= ' ORDER BY';

			foreach($this->orderBy as $key => $orderBy)
			{
				$sql.= $this->availableColumns[$orderBy[0]][0] . ' ' . $orderBy[1] . ' ' . ($len == $key ? '' : ',');
			}
		}

		// limit
		if($this->start !== null)
		{
			$sql.= ' LIMIT ' . $this->start . ', ' . $this->count;
		}
		else if($startIndex !== null && $count !== null)
		{
			$sql.= ' LIMIT ' . intval($startIndex) . ', ' . intval($count);
		}

		return $sql;
	}

	protected function buildCountQuery(Condition $condition)
	{
		// select
		$sql = 'SELECT COUNT(*) FROM `' . $this->table->getName() . '` AS `' . $this->prefix . '` ' . $this->buildJoins();

		// condition
		if($condition->hasCondition())
		{
			$sql.= $condition->getStatment();
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

	protected function project($sql, array $params = array())
	{
		$name    = $this->table->getDisplayName();
		$columns = $this->availableColumns;

		return $this->connection->project($sql, $params, function(array $row) use ($name, $columns){

			$data = array();
			foreach($columns as $alias => $spec)
			{
				if(isset($row[$alias]))
				{
					$data[$alias] = $this->convertColumnTypes($row[$alias], $spec[1]);
				}
			}

			return new Record($name, $data);

		});
	}

	protected function convertColumnTypes($value, $type)
	{
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
				return (int) $value;
				break;

			case TableInterface::TYPE_DECIMAL:
			case TableInterface::TYPE_FLOAT:
			case TableInterface::TYPE_DOUBLE:
			case TableInterface::TYPE_REAL:
				return (float) $value;
				break;

			case TableInterface::TYPE_BOOLEAN:
				return (bool) $value;
				break;

			case TableInterface::TYPE_DATE:
			case TableInterface::TYPE_DATETIME:
				return new \DateTime($value);
				break;

			default:
				return $value;
				break;
		}
	}
}

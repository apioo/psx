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

use Doctrine\DBAL\Connection;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Handler\HandlerAbstract;
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
abstract class TableAbstract extends HandlerAbstract implements TableInterface
{
	protected $connection;
	protected $select;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
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

	public function getAll(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$startIndex = $startIndex !== null ? (int) $startIndex : 0;
		$count      = !empty($count)       ? (int) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy           : $this->getPrimaryKey();
		$sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

		$fields = $this->getValidFields($fields);
		
		array_walk($fields, function(&$value){
			$value = '`' . $value . '`';
		});

		if($condition !== null)
		{
			$sql    = 'SELECT ' . implode(', ', $fields) . ' FROM `' . $this->getName() . '` ' . $condition->getStatment() . ' ';
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'SELECT ' . implode(', ', $fields) . ' FROM `' . $this->getName() . '` ';
			$params = array();
		}

		$sql.= 'ORDER BY `' . $sortBy . '` ' . ($sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC') . ' ';
		$sql.= 'LIMIT ' . intval($startIndex) . ', ' . intval($count);

		return $this->project($sql, $params);
	}

	public function get($id)
	{
		$condition = new Condition(array($this->getPrimaryKey(), '=', $id));

		return $this->getOneBy($condition);
	}

	public function getCount(Condition $condition = null)
	{
		$pk = $this->getPrimaryKey();

		if($condition !== null)
		{
			$sql    = 'SELECT COUNT(`' . $pk . '`) FROM `' . $this->getName() . '` ' . $condition->getStatment();
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'SELECT COUNT(`' . $pk . '`) FROM `' . $this->getName() . '`';
			$params = array();
		}

		return (int) $this->connection->fetchColumn($sql, $params);
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->getColumns()), $this->getRestrictedFields());
	}

	public function create(RecordInterface $record)
	{
		$params = $record->getRecordInfo()->getData();
		$fields = array_intersect_key($params, $this->getColumns());

		if(empty($fields))
		{
			throw new Exception('No valid field set');
		}

		$result = $this->connection->insert($this->getName(), $fields);

		// set id to record
		$primarySetter = 'set' . ucfirst($this->getPrimaryKey());

		if(is_callable($record, $primarySetter))
		{
			$record->$primarySetter($this->connection->lastInsertId());
		}

		return $result;
	}

	public function update(RecordInterface $record)
	{
		$params = $record->getRecordInfo()->getData();
		$fields = array_intersect_key($params, $this->getColumns());

		if(!empty($fields))
		{
			$pk = $this->getPrimaryKey();

			if(isset($fields[$pk]))
			{
				$condition = array($pk => $fields[$pk]);
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

		return $this->connection->update($this->getName(), $fields, $condition);
	}

	public function delete(RecordInterface $record)
	{
		$params = $record->getRecordInfo()->getData();
		$fields = array_intersect_key($params, $this->getColumns());

		if(!empty($fields))
		{
			$pk = $this->getPrimaryKey();

			if(isset($fields[$pk]))
			{
				$condition = array($pk => $fields[$pk]);
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

		return $this->connection->delete($this->getName(), $condition);
	}

	protected function project($sql, array $params = array())
	{
		$name    = $this->getDisplayName();
		$columns = $this->getColumns();

		return $this->connection->project($sql, $params, function(array $row) use ($name, $columns){

			$data = array();
			foreach($columns as $name => $type)
			{
				if(isset($row[$name]))
				{
					$data[$name] = $this->convertColumnTypes($row[$name], $type);
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

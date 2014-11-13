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
use PSX\Exception;
use PSX\Sql;

/**
 * TableAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TableAbstract extends TableQueryAbstract implements TableInterface
{
	use SerializeTrait;

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

	public function hasColumn($column)
	{
		$columns = $this->getColumns();

		return isset($columns[$column]);
	}

	public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$startIndex = $startIndex !== null ? (int) $startIndex : 0;
		$count      = !empty($count)       ? (int) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy           : $this->getPrimaryKey();
		$sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

		if(!in_array($sortBy, $this->getSupportedFields()))
		{
			$sortBy = $this->getPrimaryKey();
		}

		$fields  = $this->getSupportedFields();
		$builder = $this->connection->createQueryBuilder()
			->select($fields)
			->from($this->getName(), null)
			->orderBy($sortBy, $sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC')
			->setFirstResult($startIndex)
			->setMaxResults($count);

		if($condition !== null && $condition->hasCondition())
		{
			$builder->where(substr($condition->getStatment(), 5));

			$values = $condition->getValues();
			foreach($values as $key => $value)
			{
				$builder->setParameter($key, $value);
			}
		}

		return $this->project($builder->getSQL(), $builder->getParameters());
	}

	public function get($id)
	{
		$condition = new Condition(array($this->getPrimaryKey(), '=', $id));

		return $this->getOneBy($condition);
	}

	public function getCount(Condition $condition = null)
	{
		$builder = $this->connection->createQueryBuilder()
			->select($this->connection->getDatabasePlatform()->getCountExpression($this->getPrimaryKey()))
			->from($this->getName());

		if($condition !== null && $condition->hasCondition())
		{
			$builder->where(substr($condition->getStatment(), 5));

			$values = $condition->getValues();
			foreach($values as $key => $value)
			{
				$builder->setParameter($key, $value);
			}
		}

		return (int) $this->connection->fetchColumn($builder->getSQL(), $builder->getParameters());
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->getColumns()), $this->getRestrictedFields());
	}

	public function create(RecordInterface $record)
	{
		$fields = $this->serializeFields($record->getRecordInfo()->getData());

		if(!empty($fields))
		{
			$result = $this->connection->insert($this->getName(), $fields);

			// set id to record
			$primarySetter = 'set' . ucfirst($this->getPrimaryKey());

			if(is_callable($record, $primarySetter))
			{
				$record->$primarySetter($this->connection->lastInsertId());
			}

			return $result;
		}
		else
		{
			throw new Exception('No valid field set');
		}
	}

	public function update(RecordInterface $record)
	{
		$fields = $this->serializeFields($record->getRecordInfo()->getData());

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

			return $this->connection->update($this->getName(), $fields, $condition);
		}
		else
		{
			throw new Exception('No valid field set');
		}
	}

	public function delete(RecordInterface $record)
	{
		$fields = $this->serializeFields($record->getRecordInfo()->getData());

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

			return $this->connection->delete($this->getName(), $condition);
		}
		else
		{
			throw new Exception('No valid field set');
		}
	}

	/**
	 * Returns an array which can be used by the dbal insert, update and delete
	 * methods
	 *
	 * @return array
	 */
	protected function serializeFields(array $row)
	{
		$data    = array();
		$columns = $this->getColumns();

		foreach($columns as $name => $type)
		{
			if(isset($row[$name]))
			{
				$data[$name] = $this->serializeType($row[$name], $type);
			}
		}

		return $data;
	}

	protected function getFirstColumnWithAttr($searchAttr)
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

	protected function getFirstColumnWithType($searchType)
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

	protected function getValidColumns(array $columns)
	{
		return array_intersect($columns, array_keys($this->getColumns()));
	}

	protected function project($sql, array $params = array(), array $columns = null)
	{
		$name    = $this->getDisplayName();
		$columns = $columns === null ? $this->getColumns() : $columns;

		return $this->connection->project($sql, $params, function(array $row) use ($name, $columns){

			foreach($row as $key => $value)
			{
				if(isset($columns[$key]))
				{
					$row[$key] = $this->unserializeType($value, $columns[$key]);
				}
			}

			return new Record($name, $row);

		});
	}
}

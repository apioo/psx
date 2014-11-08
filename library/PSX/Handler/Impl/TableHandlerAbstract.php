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

namespace PSX\Handler\Impl;

use Doctrine\DBAL\Connection;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Handler\DataHandlerQueryAbstract;
use PSX\Handler\HandlerManipulationInterface;
use PSX\Exception;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\Select;

/**
 * TableHandlerAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TableHandlerAbstract extends DataHandlerQueryAbstract implements HandlerManipulationInterface
{
	protected $connection;
	protected $mapping;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
		$this->mapping    = $this->getMapping();
	}

	public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$startIndex = $startIndex !== null ? (int) $startIndex : 0;
		$count      = !empty($count)       ? (int) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy           : $this->mapping->getIdProperty();
		$sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

		if(!in_array($sortBy, $this->getSupportedFields()))
		{
			$sortBy = $this->mapping->getIdProperty();
		}

		$fields = $this->getSupportedFields();
		
		array_walk($fields, function(&$value){
			$value = '`' . $value . '`';
		});

		if($condition !== null)
		{
			$sql    = 'SELECT ' . implode(', ', $fields) . ' FROM `' . $this->mapping->getTableName() . '` ' . $condition->getStatment() . ' ';
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'SELECT ' . implode(', ', $fields) . ' FROM `' . $this->mapping->getTableName() . '` ';
			$params = array();
		}

		$sql.= 'ORDER BY `' . $sortBy . '` ' . ($sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC') . ' ';
		$sql.= 'LIMIT ' . intval($startIndex) . ', ' . intval($count);

		return $this->project($sql, $params);
	}

	public function get($id)
	{
		$condition = new Condition(array($this->mapping->getIdProperty(), '=', $id));

		return $this->getOneBy($condition);
	}

	public function getCount(Condition $condition = null)
	{
		$pk = $this->mapping->getIdProperty();

		if($condition !== null)
		{
			$sql    = 'SELECT COUNT(`' . $pk . '`) FROM `' . $this->mapping->getTableName() . '` ' . $condition->getStatment();
			$params = $condition->getValues();
		}
		else
		{
			$sql    = 'SELECT COUNT(`' . $pk . '`) FROM `' . $this->mapping->getTableName() . '`';
			$params = array();
		}

		return (int) $this->connection->fetchColumn($sql, $params);
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->mapping->getFields()), $this->getRestrictedFields());
	}

	public function create(RecordInterface $record)
	{
		$fields = $this->serializeFields($record->getRecordInfo()->getData(), $this->mapping->getFields());

		if(!empty($fields))
		{
			$result = $this->connection->insert($this->mapping->getTableName(), $fields);

			// set id to record
			$primarySetter = 'set' . ucfirst($this->mapping->getIdProperty());

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
		$fields = $this->serializeFields($record->getRecordInfo()->getData(), $this->mapping->getFields());

		if(!empty($fields))
		{
			$pk = $this->mapping->getIdProperty();

			if(isset($fields[$pk]))
			{
				$condition = array($pk => $fields[$pk]);
			}
			else
			{
				throw new Exception('No primary key set');
			}

			return $this->connection->update($this->mapping->getTableName(), $fields, $condition);
		}
		else
		{
			throw new Exception('No valid field set');
		}
	}

	public function delete(RecordInterface $record)
	{
		$fields = $this->serializeFields($record->getRecordInfo()->getData(), $this->mapping->getFields());

		if(!empty($fields))
		{
			$pk = $this->mapping->getIdProperty();

			if(isset($fields[$pk]))
			{
				$condition = array($pk => $fields[$pk]);
			}
			else
			{
				throw new Exception('No primary key set');
			}

			return $this->connection->delete($this->mapping->getTableName(), $condition);
		}
		else
		{
			throw new Exception('No valid field set');
		}
	}

	/**
	 * Returns the mapping informations
	 *
	 * @return PSX\Handler\Impl\Table\Mapping
	 */
	abstract protected function getMapping();

	protected function project($sql, array $params = array())
	{
		$name    = $this->getDisplayName();
		$columns = $this->mapping->getFields();

		return $this->connection->project($sql, $params, function(array $row) use ($name, $columns){

			$data = array();
			foreach($columns as $name => $type)
			{
				if(isset($row[$name]))
				{
					$data[$name] = $this->unserializeType($row[$name], $type);
				}
			}

			return new Record($name, $data);

		});
	}

	/**
	 * Returns an array which can be used by the dbal insert, update and delete
	 * methods
	 *
	 * @return array
	 */
	protected function serializeFields(array $row, array $columns)
	{
		$data = array();
		foreach($columns as $name => $type)
		{
			if(isset($row[$name]))
			{
				$data[$name] = $this->serializeType($row[$name], $type);
			}
		}

		return $data;
	}
}

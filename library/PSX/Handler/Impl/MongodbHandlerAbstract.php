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

use DateTime;
use InvalidArgumentException;
use MongoClient;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Handler\DataHandlerQueryAbstract;
use PSX\Handler\HandlerManipulationInterface;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * MongodbHandlerAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class MongodbHandlerAbstract extends DataHandlerQueryAbstract implements HandlerManipulationInterface
{
	protected $client;
	protected $mapping;

	public function __construct(MongoClient $client)
	{
		$this->client  = $client;
		$this->mapping = $this->getMapping();
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
		$fields = array_combine($fields, array_fill(0, count($fields), true));
		$query  = array();

		if($condition !== null && $condition->hasCondition())
		{
			$query = $this->getQueryByCondition($condition);
		}

		$cursor = $this->mapping->getCollection()->find($query, $fields);
		$cursor->sort(array($sortBy => $sortOrder == Sql::SORT_ASC ? 1 : -1));
		$cursor->skip($startIndex);
		$cursor->limit($count);

		$recordName    = $this->mapping->getCollection()->getName();
		$mappingFields = $this->mapping->getFields();
		$return        = array();

		while($cursor->hasNext())
		{
			$data = $cursor->getNext();
			$row  = array();

			foreach($mappingFields as $name => $type)
			{
				if(isset($data[$name]))
				{
					$row[$name] = $this->unserializeType($data[$name], $type);
				}
			}

			$return[] = new Record($recordName, $row);
		}

		return $return;
	}

	public function get($id)
	{
		$condition = new Condition(array($this->mapping->getIdProperty(), '=', $id));

		return $this->getOneBy($condition);
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->mapping->getFields()), $this->getRestrictedFields());
	}

	public function getCount(Condition $condition = null)
	{
		$query = array();

		if($condition !== null && $condition->hasCondition())
		{
			$query = array_merge($query, $this->getQueryByCondition($condition));
		}

		return $this->mapping->getCollection()->find($query)->count();
	}

	public function create(RecordInterface $record)
	{
		$fields = $this->normalizeData($record);
		$fields = array_intersect_key($fields, $this->mapping->getFields());

		$this->mapping->getCollection()->insert($fields);
	}

	public function update(RecordInterface $record)
	{
		$fields = $this->normalizeData($record);
		$fields = array_intersect_key($fields, $this->mapping->getFields());

		$idField = $this->mapping->getIdProperty();
		if(!isset($fields[$idField]))
		{
			throw new InvalidArgumentException('Id field not available');
		}

		$this->mapping->getCollection()->update(array($idField => $fields[$idField]), $fields);
	}

	public function delete(RecordInterface $record)
	{
		$fields = $this->normalizeData($record);
		$fields = array_intersect_key($fields, $this->mapping->getFields());

		$idField = $this->mapping->getIdProperty();
		if(!isset($fields[$idField]))
		{
			throw new InvalidArgumentException('Id field not available');
		}

		$this->mapping->getCollection()->remove(array($idField => $fields[$idField]));
	}

	/**
	 * Returns the mapping informations
	 *
	 * @return PSX\Handler\Impl\Mongodb\Mapping
	 */
	abstract protected function getMapping();

	/**
	 * Returns the query array based on the condition
	 *
	 * @return array
	 */
	protected function getQueryByCondition(Condition $condition)
	{
		$values      = $condition->toArray();
		$condition = null;
		$query       = array();

		foreach($values as $value)
		{
			$part = array();

			if($value[Condition::OPERATOR] == '=' || $value[Condition::OPERATOR] == 'IS')
			{
				$part[$value[Condition::COLUMN]] = $value[Condition::VALUE];
			}
			else if($value[Condition::OPERATOR] == '!=' || $value[Condition::OPERATOR] == 'IS NOT')
			{
				$part[$value[Condition::COLUMN]] = array('$ne' => $value[Condition::VALUE]);
			}
			else if($value[Condition::OPERATOR] == 'LIKE')
			{
			}
			else if($value[Condition::OPERATOR] == 'NOT LIKE')
			{
			}
			else if($value[Condition::OPERATOR] == '<')
			{
				$part[$value[Condition::COLUMN]] = array('$lt' => $value[Condition::VALUE]);
			}
			else if($value[Condition::OPERATOR] == '>')
			{
				$part[$value[Condition::COLUMN]] = array('$gt' => $value[Condition::VALUE]);
			}
			else if($value[Condition::OPERATOR] == '<=')
			{
				$part[$value[Condition::COLUMN]] = array('$lte' => $value[Condition::VALUE]);
			}
			else if($value[Condition::OPERATOR] == '>=')
			{
				$part[$value[Condition::COLUMN]] = array('$gte' => $value[Condition::VALUE]);
			}
			else if($value[Condition::OPERATOR] == 'IN')
			{
			}

			$condition = $value[Condition::CONJUNCTION];

			if(isset($query['$and']))
			{
				$query['$and'][] = $part;
			}
			else if(isset($query['$or']))
			{
				$query['$or'][] = $part;
			}
			else if($condition == 'AND' || $condition == '&&')
			{
				$query['$and'] = array();
				$query['$and'][] = $part;
			}
			else if($condition == 'OR' || $condition == '||')
			{
				$query['$or'] = array();
				$query['$or'][] = $part;
			}
		}

		return $query;
	}

	protected function normalizeData(RecordInterface $record)
	{
		$fields = $record->getRecordInfo()->getFields();
		$data   = array();

		foreach($fields as $k => $v)
		{
			if(isset($v))
			{
				if($v instanceof RecordInterface)
				{
					$data[$k] = $this->normalizeData($v);
				}
				else if($v instanceof DateTime)
				{
					$data[$k] = $v->format(DateTime::RFC3339);
				}
				else if(is_object($v))
				{
					$data[$k] = (string) $v;
				}
				else
				{
					$data[$k] = $v;
				}
			}
		}

		return $data;
	}
}

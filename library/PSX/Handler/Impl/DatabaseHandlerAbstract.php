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
use Doctrine\DBAL\Connection;
use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\ResultSet;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Handler\DataHandlerQueryAbstract;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;
use PSX\Sql\TableManagerInterface;
use RuntimeException;

/**
 * Database handler which operates on an DBAL connection. Is intended to build
 * complex queries which can not be handeled by the table or select handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DatabaseHandlerAbstract extends DataHandlerQueryAbstract
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

		$sql    = $this->getSelectQuery($startIndex, $count, $sortBy, $sortOrder, $condition);
		$params = $condition !== null ? $condition->getValues() : array();

		return $this->project($sql, $params);
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
		$sql    = $this->getCountQuery($condition);
		$params = $condition !== null ? $condition->getValues() : array();

		return (int) $this->connection->fetchColumn($sql, $params);
	}

	protected function getSelectQuery($startIndex, $count, $sortBy, $sortOrder, Condition $condition = null)
	{
		$sql = $this->mapping->getSql();

		if($condition !== null)
		{
			$sql    = str_replace('{condition}', $condition->getStatment(), $sql);
			$params = $condition->getValues();
		}
		else
		{
			$sql    = str_replace('{condition}', 'WHERE 1', $sql);
			$params = array();
		}

		if($sortBy !== null)
		{
			$sql = str_replace('{orderBy}', 'ORDER BY `' . $sortBy . '` ' . ($sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC'), $sql);
		}
		else
		{
			$sql = str_replace('{orderBy}', '', $sql);
		}

		if($startIndex !== null)
		{
			$sql = str_replace('{limit}', 'LIMIT ' . intval($startIndex) . ', ' . intval($count), $sql);
		}
		else
		{
			$sql = str_replace('{limit}', '', $sql);
		}

		return $sql;
	}

	protected function getCountQuery(Condition $condition = null)
	{
		$sql = $this->mapping->getSql();

		$sql    = preg_match_all('/SELECT(.*)FROM(.*)/ims', $sql, $matches);
		$select = isset($matches[1][0]) ? $matches[1][0] : null;
		$from   = isset($matches[2][0]) ? $matches[2][0] : null;
		$sql    = 'SELECT COUNT(*) FROM ' . $from;

		$sql = str_replace('{orderBy}', '', $sql);
		$sql = str_replace('{limit}', '', $sql);

		if($condition !== null)
		{
			$sql    = str_replace('{condition}', $condition->getStatment(), $sql);
			$params = $condition->getValues();
		}
		else
		{
			$sql    = str_replace('{condition}', 'WHERE 1', $sql);
			$params = array();
		}

		return $sql;
	}

	protected function getRecordName()
	{
		return 'record';
	}

	protected function project($sql, array $params)
	{
		$name          = $this->getRecordName();
		$mappingFields = $this->mapping->getFields();

		return $this->connection->project($sql, $params, function(array $row) use ($name, $mappingFields){

			$data = array();
			foreach($mappingFields as $name => $type)
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
	 * Returns the mapping informations about this query. The mapping contains
	 * the select query. The query can contain the following fields which get 
	 * replaced {condition}, {orderBy}, {limit}
	 *
	 * @return PSX\Handler\Impl\Database\Mapping
	 */
	abstract protected function getMapping();
}

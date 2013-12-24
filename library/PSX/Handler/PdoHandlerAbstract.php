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

namespace PSX\Handler;

use DateTime;
use PDO;
use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\ResultSet;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Sql;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;

/**
 * Handler wich uses native sql queries to obtain the fields
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class PdoHandlerAbstract extends DataHandlerQueryAbstract
{
	protected $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo     = $pdo;
		$this->mapping = $this->getMapping();
	}

	public function getAll(array $fields = array(), $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy               : $this->mapping->getIdProperty();
		$sortOrder  = $sortOrder  !== null ? (integer) $sortOrder  : Sql::SORT_DESC;

		if(empty($fields))
		{
			$fields = $this->getSupportedFields();
		}

		if(!in_array($sortBy, $this->getSupportedFields()))
		{
			$sortBy = $this->mapping->getIdProperty();
		}

		$statment = $this->getSelectStatment($fields, $startIndex, $count, $sortBy, $sortOrder, $con);
		$statment->execute();

		$result = $statment->fetchAll(PDO::FETCH_ASSOC);
		$return = array();

		foreach($result as $entry)
		{
			$row = array();

			foreach($entry as $key => $value)
			{
				foreach($this->mapping->getFields() as $field => $type)
				{
					if($key == $field)
					{
						$row[$field] = $this->unserializeType($value, $type);
					}
				}
			}

			$return[] = new Record('record', $row);
		}

		return $return;
	}

	public function get($id, array $fields = array())
	{
		$con = new Condition(array($this->mapping->getIdProperty(), '=', $id));

		return $this->getOneBy($con, $fields);
	}

	public function getSupportedFields()
	{
		return array_keys($this->mapping->getFields());
	}

	public function getCount(Condition $con = null)
	{
		$statment = $this->getCountStatment($con);
		$statment->execute();

		$result = $statment->fetch(PDO::FETCH_NUM);

		if(isset($result[0]))
		{
			return (integer) $result[0];
		}

		return 0;
	}

	public function getRecord($id = null)
	{
		if(empty($id))
		{
			$fields  = $this->mapping->getFields();
			$keys    = array_keys($fields);
			$values  = array_fill(0, count($fields), null);

			return new Record('record', array_combine($keys, $values));
		}
		else
		{
			$fields  = array_keys($this->mapping->getFields());

			return $this->get($id, $fields);
		}
	}

	/**
	 * Returns the mapping informations about this query
	 *
	 * @return string
	 */
	abstract public function getMapping();

	/**
	 * Returns the sql select query
	 *
	 * @return PDOStatement
	 */
	abstract protected function getSelectStatment(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null);

	/**
	 * Returns the sql count query
	 *
	 * @return PDOStatement
	 */
	abstract protected function getCountStatment(Condition $con = null);
}

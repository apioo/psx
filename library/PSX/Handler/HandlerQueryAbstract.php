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

namespace PSX\Handler;

use BadMethodCallException;
use InvalidArgumentException;
use PSX\Data\Collection;
use PSX\Data\Record;
use PSX\Data\ResultSet;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * HandlerQueryAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class HandlerQueryAbstract implements HandlerQueryInterface
{
	protected $restrictedFields = array();

	public function getBy(Condition $condition)
	{
		return $this->getAll(null, null, null, null, $condition);
	}

	public function getOneBy(Condition $condition)
	{
		$result = $this->getAll(0, 1, null, null, $condition);

		return current($result);
	}

	public function getRecord()
	{
		$supported = $this->getSupportedFields();
		$fields    = array_combine($supported, array_fill(0, count($supported), null));

		return new Record('record', $fields);
	}

	/**
	 * Returns an collection of records
	 *
	 * @param array $fields
	 * @param integer $startIndex
	 * @param integer $count
	 * @param string $sortBy
	 * @param string $sortOrder
	 * @param PSX\Sql\Condition $condition
	 * @return PSX\Data\Collection
	 */
	public function getCollection($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$entries    = $this->getAll($startIndex, $count, $sortBy, $sortOrder, $condition);
		$collection = new Collection($entries);

		return $collection;
	}

	/**
	 * Returns an collection of record including the start index and total 
	 * result count wich is useful for building paginations
	 *
	 * @param array $fields
	 * @param integer $startIndex
	 * @param integer $count
	 * @param string $sortBy
	 * @param string $sortOrder
	 * @param PSX\Sql\Condition $condition
	 * @return PSX\Data\ResultSet
	 */
	public function getResultSet($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$total      = $this->getCount($condition);
		$entries    = $this->getAll($startIndex, $count, $sortBy, $sortOrder, $condition);
		$resultSet  = new ResultSet($total, $startIndex, $count, $entries);

		return $resultSet;
	}

	/**
	 * Magic method to make conditional selection
	 *
	 * @param string $method
	 * @param string $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		if(substr($method, 0, 8) == 'getOneBy')
		{
			$column = lcfirst(substr($method, 8));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : null;

			if(!empty($value))
			{
				$condition = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getOneBy($condition, $fields);
		}
		else if(substr($method, 0, 5) == 'getBy')
		{
			$column = lcfirst(substr($method, 5));
			$value  = isset($arguments[0]) ? $arguments[0] : null;
			$fields = isset($arguments[1]) ? $arguments[1] : null;

			if(!empty($value))
			{
				$condition = new Condition(array($column, '=', $value));
			}
			else
			{
				throw new InvalidArgumentException('Value required');
			}

			return $this->getBy($condition, $fields);
		}
		else
		{
			throw new BadMethodCallException('Undefined method ' . $method);
		}
	}

	/**
	 * Returns an array of fields wich can not be used from the handler even if 
	 * the fields can be selected through the handler. This is useful for fields
	 * with sensetive data i.e. passwords
	 *
	 * @return array
	 */
	public function getRestrictedFields()
	{
		return $this->restrictedFields;
	}

	/**
	 * Sets the restricted fields
	 *
	 * @param array $restrictedFields
	 */
	public function setRestrictedFields(array $restrictedFields)
	{
		$this->restrictedFields = $restrictedFields;
	}
}

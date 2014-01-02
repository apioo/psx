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

use PSX\Data\Record;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * Handler wich can query an array to select fields
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class MapHandlerAbstract extends DataHandlerQueryAbstract
{
	protected $mapping;

	public function __construct()
	{
		$this->mapping = $this->getMapping();
	}

	public function getAll(array $fields = array(), $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy               : $this->mapping->getIdProperty();
		$sortOrder  = $sortOrder  !== null ? (integer) $sortOrder  : Sql::SORT_DESC;

		$fields = array_intersect($fields, $this->getSupportedFields());

		if(empty($fields))
		{
			$fields = $this->getSupportedFields();
		}

		$array   = $this->mapping->getArray();
		$sort    = array();
		$return  = array();

		foreach($array as $entry)
		{
			$row       = array();
			$sortValue = null;

			foreach($entry as $key => $value)
			{
				foreach($this->mapping->getFields() as $field => $type)
				{
					if($key == $field)
					{
						$row[$field] = $this->unserializeType($value, $type);

						if($sortBy == $field)
						{
							$sortValue = $row[$field];
						}
					}
				}
			}

			if($con !== null && $con->hasCondition())
			{
				if(!$this->isConditionFulfilled($con, $row))
				{
					continue;
				}
			}

			$return[] = $row;
			$sort[]   = $sortValue;
		}

		// sort
		if($sortOrder == Sql::SORT_ASC)
		{
			asort($sort);
		}
		else
		{
			arsort($sort);
		}

		$result = array();
		foreach($sort as $key => $value)
		{
			$row = array_intersect_key($return[$key], array_flip($fields));

			$result[] = new Record('record', $row);
		}

		return array_slice($result, $startIndex, $count);
	}

	public function get($id, array $fields = array())
	{
		$con = new Condition(array($this->mapping->getIdProperty(), '=', $id));

		return $this->getOneBy($con, $fields);
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->mapping->getFields()), $this->getRestrictedFields());
	}

	public function getCount(Condition $con = null)
	{
		$count = 0;
		$array = $this->mapping->getArray();

		foreach($array as $entry)
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

			if($con !== null && $con->hasCondition())
			{
				if(!$this->isConditionFulfilled($con, $row))
				{
					continue;
				}
			}

			$count++;
		}

		return $count;
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
	 * Returns the mapping informations for this document
	 *
	 * @return PSX\Handler\Map\Mapping
	 */
	abstract public function getMapping();
}

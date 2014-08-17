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

	public function getAll(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$startIndex = $startIndex !== null ? (int) $startIndex : 0;
		$count      = !empty($count)       ? (int) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy           : $this->mapping->getIdProperty();
		$sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

		$fields        = $this->getValidFields($fields);
		$array         = $this->mapping->getArray();
		$mappingFields = $this->mapping->getFields();
		$sort          = array();
		$return        = array();

		foreach($array as $entry)
		{
			$row       = array();
			$sortValue = null;

			foreach($mappingFields as $name => $type)
			{
				if(isset($entry[$name]))
				{
					$row[$name] = $this->unserializeType($entry[$name], $type);

					if($sortBy == $name)
					{
						$sortValue = $row[$name];
					}
				}
			}

			if($condition !== null && $condition->hasCondition())
			{
				if(!$this->isConditionFulfilled($condition, $row))
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
		$count         = 0;
		$array         = $this->mapping->getArray();
		$mappingFields = $this->mapping->getFields();

		foreach($array as $entry)
		{
			$row = array();

			foreach($mappingFields as $name => $type)
			{
				if(isset($entry[$name]))
				{
					$row[$name] = $this->unserializeType($entry[$name], $type);
				}
			}

			if($condition !== null && $condition->hasCondition())
			{
				if(!$this->isConditionFulfilled($condition, $row))
				{
					continue;
				}
			}

			$count++;
		}

		return $count;
	}

	/**
	 * Returns the mapping informations for this document
	 *
	 * @return PSX\Handler\Map\Mapping
	 */
	abstract public function getMapping();
}

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

namespace PSX\Util\Api;

use PSX\DateTime;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * FilterParameter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FilterParameter
{
	protected $fields;
	protected $startIndex;
	protected $count;
	protected $sortBy;
	protected $sortOrder;
	protected $filterBy;
	protected $filterOp;
	protected $filterValue;
	protected $updatedSince;

	public function setFields(array $fields = null)
	{
		$this->fields = $fields;
	}
	
	public function getFields()
	{
		return $this->fields;
	}

	public function setStartIndex($startIndex)
	{
		$this->startIndex = $startIndex;
	}
	
	public function getStartIndex()
	{
		return $this->startIndex;
	}

	public function setCount($count)
	{
		$this->count = $count;
	}
	
	public function getCount()
	{
		return $this->count;
	}

	public function setSortBy($sortBy)
	{
		$this->sortBy = $sortBy;
	}
	
	public function getSortBy()
	{
		return $this->sortBy;
	}

	public function setSortOrder($sortOrder)
	{
		$this->sortOrder = $sortOrder;
	}
	
	public function getSortOrder()
	{
		return $this->sortOrder;
	}

	public function setFilterBy($filterBy)
	{
		$this->filterBy = $filterBy;
	}
	
	public function getFilterBy()
	{
		return $this->filterBy;
	}

	public function setFilterOp($filterOp)
	{
		$this->filterOp = $filterOp;
	}
	
	public function getFilterOp()
	{
		return $this->filterOp;
	}

	public function setFilterValue($filterValue)
	{
		$this->filterValue = $filterValue;
	}
	
	public function getFilterValue()
	{
		return $this->filterValue;
	}

	public function setUpdatedSince($updatedSince)
	{
		$this->updatedSince = $updatedSince;
	}
	
	public function getUpdatedSince()
	{
		return $this->updatedSince;
	}

	public static function extract(array $parameters)
	{
		$filter = new self();

		$fields       = isset($parameters['fields']) ? $parameters['fields'] : null;
		$startIndex   = isset($parameters['startIndex']) ? $parameters['startIndex'] : null;
		$count        = isset($parameters['count']) ? $parameters['count'] : null;
		$sortBy       = isset($parameters['sortBy']) ? $parameters['sortBy'] : null;
		$sortOrder    = isset($parameters['sortOrder']) ? $parameters['sortOrder'] : null;
		$filterBy     = isset($parameters['filterBy']) ? $parameters['filterBy'] : null;
		$filterOp     = isset($parameters['filterOp']) ? $parameters['filterOp'] : null;
		$filterValue  = isset($parameters['filterValue']) ? $parameters['filterValue'] : null;
		$updatedSince = isset($parameters['updatedSince']) ? $parameters['updatedSince'] : null;

		if(!empty($fields))
		{
			$parts  = explode(',', $fields);
			$fields = array();

			foreach($parts as $field)
			{
				$field = trim($field);

				if(strlen($field) > 0 && strlen($field) < 32 && ctype_alnum($field))
				{
					$fields[] = $field;
				}
			}
		}
		else
		{
			$fields = null;
		}

		$filter->setFields($fields);

		$startIndex = (int) $startIndex;
		if(!empty($startIndex) && $startIndex > 0)
		{
			$filter->setStartIndex($startIndex);
		}

		$count = (int) $count;
		if(!empty($count) && $count > 0)
		{
			$filter->setCount($count);
		}

		if(!empty($sortBy) && strlen($sortBy) < 128)
		{
			$filter->setSortBy($sortBy);
		}

		if(!empty($sortOrder))
		{
			switch(strtolower($sortOrder))
			{
				case 'asc':
				case 'ascending':
					$filter->setSortOrder(Sql::SORT_ASC);
					break;

				case 'desc':
				case 'descending':
					$filter->setSortOrder(Sql::SORT_DESC);
					break;
			}
		}

		if(!empty($filterBy) && ctype_alnum($filterBy) && strlen($filterBy) < 32)
		{
			$filter->setFilterBy($filterBy);
		}

		if(!empty($filterOp) && in_array($filterOp, array('contains', 'equals', 'startsWith', 'present')))
		{
			$filter->setFilterOp($filterOp);
		}

		if(!empty($filterValue) && strlen($filterValue) < 128)
		{
			$filter->setFilterValue($filterValue);
		}

		if(!empty($updatedSince))
		{
			$filter->setUpdatedSince(new \DateTime($updatedSince));
		}

		return $filter;
	}

	public static function getCondition(FilterParameter $parameter, $dateColumn = 'date')
	{
		$condition = new Condition();

		if($parameter->getFilterBy() && $parameter->getFilterValue())
		{
			switch($parameter->getFilterOp())
			{
				case 'contains':
					$condition->add($parameter->getFilterBy(), 'LIKE', '%' . $parameter->getFilterValue() . '%');
					break;

				case 'equals':
					$condition->add($parameter->getFilterBy(), '=', $parameter->getFilterValue());
					break;

				case 'startsWith':
					$condition->add($parameter->getFilterBy(), 'LIKE', $parameter->getFilterValue() . '%');
					break;

				case 'present':
					$condition->add($parameter->getFilterBy(), 'IS NOT', 'NULL', 'AND');
					$condition->add($parameter->getFilterBy(), 'NOT LIKE', '');
					break;
			}
		}

		if($parameter->getUpdatedSince() instanceof \DateTime)
		{
			$condition->add($dateColumn, '>', $parameter->getUpdatedSince()->format(DateTime::SQL));
		}

		return $condition;
	}
}

<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Filter;

use PSX\FilterAbstract;

/**
 * Collection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Collection extends FilterAbstract
{
	protected $filters;

	/**
	 * @param array<PSX\FilterInterface>
	 */
	public function __construct(array $filters)
	{
		$this->filters = $filters;
	}

	/**
	 * Returns true if all filters allow the value
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function apply($value)
	{
		$modified = false;

		foreach($this->filters as $filter)
		{
			$result = $filter->apply($value);

			if($result === false)
			{
				return false;
			}
			else if($result === true)
			{
			}
			else
			{
				$modified = true;
				$value    = $result;
			}
		}

		return $modified ? $value : true;
	}

	public function getErrorMessage()
	{
		return '%s contains invalid values';
	}
}

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

namespace PSX\Filter;

use PSX\FilterAbstract;
use PSX\FilterInterface;

/**
 * ArrayFilter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ArrayFilter extends FilterAbstract
{
	protected $filter;

	public function __construct(FilterInterface $filter)
	{
		$this->filter = $filter;
	}

	/**
	 * Returns true if all values in $value apply to the filter. If the parent
	 * filter has changed an value the modified array gets returned
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function apply($value)
	{
		$data     = array();
		$modified = false;

		if(is_array($value))
		{
			foreach($value as $key => $val)
			{
				$result = $this->filter->apply($val);

				if($result === false)
				{
					return false;
				}
				else if($result === true)
				{
					$data[$key] = $val;
				}
				else
				{
					$modified = true;

					$data[$key] = $result;
				}
			}
		}
		else
		{
			return false;
		}

		return $modified ? $data : true;
	}

	public function getErrorMessage()
	{
		return '%s contains invalid values';
	}
}

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

namespace PSX\Filter;

use PSX\FilterAbstract;

/**
 * Filter wich returns either an datetime object or if a format is specified 
 * the date in the given format as string
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class DateTime extends FilterAbstract
{
	protected $format;

	public function __construct($format = null)
	{
		$this->format = $format;
	}

	public function apply($value)
	{
		try
		{
			$date = $value instanceof \DateTime ? $value : new \DateTime((string) $value);

			if($this->format === null)
			{
				return $date;
			}
			else
			{
				return $date->format($this->format);
			}
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	public function getErrorMsg()
	{
		return '%s has not a valid date format';
	}
}

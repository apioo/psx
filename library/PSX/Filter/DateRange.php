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

use InvalidArgumentException;
use PSX\FilterAbstract;

/**
 * Checks whether the given date is in an specific range
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class DateRange extends DateTime
{
	protected $from;
	protected $to;

	public function __construct(\DateTime $from = null, \DateTime $to = null, $format = null)
	{
		parent::__construct($format);

		if($from === null && $to === null)
		{
			throw new InvalidArgumentException('You need to specify a from or to date');
		}

		$this->from = $from;
		$this->to   = $to;
	}

	public function apply($value)
	{
		$date = parent::apply($value);
		
		if($date instanceof \DateTime)
		{
			$inRange = false;

			if($this->from !== null && $this->to !== null)
			{
				$inRange = $date >= $this->from && $date <= $this->to;
			}
			else if($this->from !== null && $this->to === null)
			{
				$inRange = $date >= $this->from;
			}
			else if($this->from === null && $this->to !== null)
			{
				$inRange = $date <= $this->to;
			}

			return $inRange ? $date : false;
		}
		else
		{
			return false;
		}
	}

	public function getErrorMessage()
	{
		if($this->from !== null && $this->to !== null)
		{
			return '%s is not between ' . $this->from->format('Y-m-d H:i:s') . ' and ' . $this->to->format('Y-m-d H:i:s');
		}
		else if($this->from !== null && $this->to === null)
		{
			return '%s is not greater or equal ' . $this->from->format('Y-m-d H:i:s');
		}
		else if($this->from === null && $this->to !== null)
		{
			return '%s is not lower or equal ' . $this->to->format('Y-m-d H:i:s');
		}
	}
}

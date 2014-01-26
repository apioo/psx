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

namespace PSX;

use DateInterval;
use DateTime;
use DateTimeZone;
use Iterator;
use Countable;

/**
 * Calendar
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Calendar implements Iterator, Countable
{
	protected $date;

	private $itDate;
	private $pos;

	public function __construct(DateTime $date = null, DateTimeZone $timezone = null)
	{
		$this->setDate($date !== null ? $date : new DateTime());

		if($timezone !== null)
		{
			$this->setTimezone($timezone);
		}
	}

	/**
	 * Sets the underlying datetime object and removes the timepart of the
	 * datetime object
	 *
	 * @return void
	 */
	public function setDate(DateTime $date)
	{
		$this->date = $date->setTime(0, 0, 0);
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setTimezone(DateTimeZone $timezone)
	{
		$this->date->setTimezone($timezone);
	}

	public function getTimezone()
	{
		return $this->date->getTimezone();
	}

	/**
	 * Return the days of the current month and year
	 *
	 * @return integer
	 */
	public function getDays()
	{
		return cal_days_in_month(CAL_GREGORIAN, $this->date->format('n'), $this->date->format('Y'));
	}

	/**
	 * Returns the easter date for the current year
	 *
	 * @return PSX\DateTime
	 */
	public function getEasterDate()
	{
		$easter = new DateTime($this->getYear() . '-03-21');
		$days   = easter_days($this->getYear());

		return $easter->add(new DateInterval('P' . $days . 'D'));
	}

	public function getWeekNumber()
	{
		return $this->date->format('W');
	}

	public function getDay()
	{
		return $this->date->format('j');
	}

	public function getMonth()
	{
		return $this->date->format('n');
	}

	public function getYear()
	{
		return $this->date->format('Y');
	}

	public function getMonthName()
	{
		return $this->date->format('F');
	}

	public function add(DateInterval $interval)
	{
		$this->date->add($interval);

		return $this;
	}

	public function sub(DateInterval $interval)
	{
		$this->date->sub($interval);

		return $this;
	}

	public function nextDay()
	{
		return $this->add(new DateInterval('P1D'));
	}

	public function prevDay()
	{
		return $this->sub(new DateInterval('P1D'));
	}

	public function nextMonth()
	{
		return $this->add(new DateInterval('P1M'));
	}

	public function prevMonth()
	{
		return $this->sub(new DateInterval('P1M'));
	}

	public function nextYear()
	{
		return $this->add(new DateInterval('P1Y'));
	}

	public function prevYear()
	{
		return $this->sub(new DateInterval('P1Y'));
	}

	// countable
	public function count()
	{
		return $this->getDays();
	}

	// iterator
	public function current()
	{
		return $this->itDate;
	}

	public function key()
	{
		return $this->itDate->format('j');
	}

	public function next()
	{
		$this->itDate->add(new DateInterval('P1D'))->setTime(0, 0, 0);
	}

	public function rewind()
	{
		$this->itDate = new DateTime($this->getYear() . '-' . $this->getMonth() . '-01');
	}

	public function valid()
	{
		return $this->date->format('n') == $this->itDate->format('n');
	}
}


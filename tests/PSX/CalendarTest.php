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

/**
 * CalendarTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CalendarTest extends \PHPUnit_Framework_TestCase
{
	public function testGetter()
	{
		$calendar = new Calendar(new \DateTime('2014-01-01 12:14:00'), new \DateTimeZone('UTC'));

		$this->assertEquals('2014-01-01 00:00:00', $calendar->getDate()->format('Y-m-d H:i:s'));

		$calendar->setDate(new \DateTime('2014-01-04 12:14:00'));

		$this->assertEquals('2014-01-04 00:00:00', $calendar->getDate()->format('Y-m-d H:i:s'));

		$tz = new \DateTimeZone('Europe/Berlin');

		$calendar->setTimezone($tz);

		$this->assertEquals($tz, $calendar->getTimezone());
		$this->assertEquals(31, $calendar->getDays());
		$this->assertEquals(31, count($calendar));

		$this->assertEquals('01', $calendar->getWeekNumber());
		$this->assertEquals(4, $calendar->getDay());
		$this->assertEquals(1, $calendar->getMonth());
		$this->assertEquals(2014, $calendar->getYear());
		$this->assertEquals('January', $calendar->getMonthName());
	}

	public function testGetEasterDate()
	{
		$calendar = new Calendar(new \DateTime('2014-01-04 12:14:00'));

		$this->assertEquals('2014-04-20', $calendar->getEasterDate()->format('Y-m-d'));
	}

	public function testDateNavigation()
	{
		$calendar = new Calendar(new \DateTime('2014-01-04 12:14:00'));

		$this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

		$calendar->nextDay();

		$this->assertEquals('2014-01-05', $calendar->getDate()->format('Y-m-d'));

		$calendar->prevDay();

		$this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

		$calendar->nextMonth();

		$this->assertEquals('2014-02-04', $calendar->getDate()->format('Y-m-d'));

		$calendar->prevMonth();

		$this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

		$calendar->nextYear();

		$this->assertEquals('2015-01-04', $calendar->getDate()->format('Y-m-d'));

		$calendar->prevYear();

		$this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));

		$calendar->add(new \DateInterval('P1M2D'));

		$this->assertEquals('2014-02-06', $calendar->getDate()->format('Y-m-d'));

		$calendar->sub(new \DateInterval('P1M2D'));

		$this->assertEquals('2014-01-04', $calendar->getDate()->format('Y-m-d'));
	}

	public function testDateIterator()
	{
		$calendar = new Calendar(new \DateTime('2014-01-04 12:14:00'));
		$days = count($calendar);
		$i = 0;

		foreach($calendar as $day)
		{
			$i++;
			$this->assertEquals($i, $day->format('j'));
		}

		$this->assertEquals($i, $days);
	}
}

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

namespace PSX;

use DateInterval;

/**
 * DateTimeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testConvertIntervalToSeconds()
	{
		$this->assertEquals(1, DateTime::convertIntervalToSeconds(new DateInterval('PT1S')));
		$this->assertEquals(60, DateTime::convertIntervalToSeconds(new DateInterval('PT60S')));
		$this->assertEquals(60, DateTime::convertIntervalToSeconds(new DateInterval('PT1M')));
		$this->assertEquals(3600, DateTime::convertIntervalToSeconds(new DateInterval('PT60M')));
		$this->assertEquals(3600, DateTime::convertIntervalToSeconds(new DateInterval('PT1H')));
		$this->assertEquals(86400, DateTime::convertIntervalToSeconds(new DateInterval('PT24H')));
		$this->assertEquals(86400, DateTime::convertIntervalToSeconds(new DateInterval('P1D')));
		$this->assertEquals(2592000, DateTime::convertIntervalToSeconds(new DateInterval('P30D')));
		$this->assertEquals(2592000, DateTime::convertIntervalToSeconds(new DateInterval('P1M')));
		$this->assertEquals(31104000, DateTime::convertIntervalToSeconds(new DateInterval('P12M')));
		$this->assertEquals(31536000, DateTime::convertIntervalToSeconds(new DateInterval('P1Y')));
	}
}


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

/**
 * DateRangeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DateRangeTest extends FilterTestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testFilter()
	{
		$from   = new \DateTime('2010-10-10 06:00:00');
		$to     = new \DateTime('2010-10-12 08:30:00');
		$filter = new DateRange($from, $to);

		// test from
		$this->assertEquals(false, $filter->apply('2010-10-10 05:59:59'));
		$this->assertEquals(new \DateTime('2010-10-10 06:00:00'), $filter->apply('2010-10-10 06:00:00'));
		$this->assertEquals(new \DateTime('2010-10-10 06:00:01'), $filter->apply('2010-10-10 06:00:01'));

		// test between
		$this->assertEquals(new \DateTime('2010-10-11 06:00:00'), $filter->apply('2010-10-11 06:00:00'));

		// test to
		$this->assertEquals(new \DateTime('2010-10-12 08:29:59'), $filter->apply('2010-10-12 08:29:59'));
		$this->assertEquals(new \DateTime('2010-10-12 08:30:00'), $filter->apply('2010-10-12 08:30:00'));
		$this->assertEquals(false, $filter->apply('2010-10-12 08:30:01'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}

	public function testFilterFrom()
	{
		$from   = new \DateTime('2010-10-10 06:00:00');
		$filter = new DateRange($from);

		// test from
		$this->assertEquals(false, $filter->apply('2010-10-10 05:59:59'));
		$this->assertEquals(new \DateTime('2010-10-10 06:00:00'), $filter->apply('2010-10-10 06:00:00'));
		$this->assertEquals(new \DateTime('2010-10-10 06:00:01'), $filter->apply('2010-10-10 06:00:01'));

		// test between
		$this->assertEquals(new \DateTime('2010-10-11 06:00:00'), $filter->apply('2010-10-11 06:00:00'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}

	public function testFilterTo()
	{
		$to     = new \DateTime('2010-10-12 08:30:00');
		$filter = new DateRange(null, $to);

		// test between
		$this->assertEquals(new \DateTime('2010-10-11 06:00:00'), $filter->apply('2010-10-11 06:00:00'));

		// test to
		$this->assertEquals(new \DateTime('2010-10-12 08:29:59'), $filter->apply('2010-10-12 08:29:59'));
		$this->assertEquals(new \DateTime('2010-10-12 08:30:00'), $filter->apply('2010-10-12 08:30:00'));
		$this->assertEquals(false, $filter->apply('2010-10-12 08:30:01'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}
}

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
 * Md5Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Md5Test extends FilterTestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testFilter()
	{
		$filter = new Md5();

		$this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $filter->apply(''));
		$this->assertEquals('7e716d0e702df0505fc72e2b89467910', $filter->apply('Frank jagt im komplett verwahrlosten Taxi quer durch Bayern'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMsg());
	}
}

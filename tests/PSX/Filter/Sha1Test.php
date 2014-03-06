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
 * Sha1Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sha1Test extends FilterTestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testFilter()
	{
		$filter = new Sha1();

		$this->assertEquals('da39a3ee5e6b4b0d3255bfef95601890afd80709', $filter->apply(''));
		$this->assertEquals('d8e8ece39c437e515aa8997c1a1e94f1ed2a0e62', $filter->apply('Frank jagt im komplett verwahrlosten Taxi quer durch Bayern'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMsg());
	}
}
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

/**
 * ArrayFilterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ArrayFilterTest extends FilterTestCase
{
	public function testFilter()
	{
		$filter = new ArrayFilter(new Alnum());

		$this->assertEquals(true, $filter->apply(array('foo')));
		$this->assertEquals(false, $filter->apply(array('12 3')));
		$this->assertEquals(false, $filter->apply(array('foo', '12 3')));
		$this->assertEquals(false, $filter->apply('foo'));

		$filter = new ArrayFilter(new Sha1());

		$this->assertEquals(array('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33'), $filter->apply(array('foo')));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}
}
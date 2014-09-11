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
 * LengthTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LengthTest extends FilterTestCase
{
	public function testFilterIntegerLength()
	{
		$filter = new Length(3, 8);

		$this->assertEquals(false, $filter->apply(2));
		$this->assertEquals(true, $filter->apply(3));
		$this->assertEquals(true, $filter->apply(8));
		$this->assertEquals(false, $filter->apply(9));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());

		$filter = new Length(8);

		$this->assertEquals(true, $filter->apply(2));
		$this->assertEquals(true, $filter->apply(3));
		$this->assertEquals(true, $filter->apply(8));
		$this->assertEquals(false, $filter->apply(9));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}

	public function testFilterFloatLength()
	{
		$filter = new Length(3.4, 8.4);

		$this->assertEquals(false, $filter->apply(3.3));
		$this->assertEquals(true, $filter->apply(3.4));
		$this->assertEquals(true, $filter->apply(8.4));
		$this->assertEquals(false, $filter->apply(8.5));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());

		$filter = new Length(8.4);

		$this->assertEquals(true, $filter->apply(3.3));
		$this->assertEquals(true, $filter->apply(3.4));
		$this->assertEquals(true, $filter->apply(8.4));
		$this->assertEquals(false, $filter->apply(8.5));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}

	public function testFilterArrayLength()
	{
		$filter = new Length(3, 8);

		$this->assertEquals(false, $filter->apply(range(0, 1)));
		$this->assertEquals(true, $filter->apply(range(0, 2)));
		$this->assertEquals(true, $filter->apply(range(0, 7)));
		$this->assertEquals(false, $filter->apply(range(0, 8)));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());

		$filter = new Length(8);

		$this->assertEquals(true, $filter->apply(range(0, 1)));
		$this->assertEquals(true, $filter->apply(range(0, 2)));
		$this->assertEquals(true, $filter->apply(range(0, 7)));
		$this->assertEquals(false, $filter->apply(range(0, 8)));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}

	public function testFilterStringLength()
	{
		$filter = new Length(3, 8);

		$this->assertEquals(false, $filter->apply('fo'));
		$this->assertEquals(true, $filter->apply('foo'));
		$this->assertEquals(true, $filter->apply('foobarte'));
		$this->assertEquals(false, $filter->apply('foobartes'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());

		$filter = new Length(8);

		$this->assertEquals(true, $filter->apply('fo'));
		$this->assertEquals(true, $filter->apply('foo'));
		$this->assertEquals(true, $filter->apply('foobarte'));
		$this->assertEquals(false, $filter->apply('foobartes'));

		// test error message
		$this->assertErrorMessage($filter->getErrorMessage());
	}
}

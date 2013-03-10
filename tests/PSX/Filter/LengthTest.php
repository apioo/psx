<?php
/*
 *  $Id: LengthTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_Filter_LengthTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class LengthTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testIntLength()
	{
		$length = new Length(3, 8);

		$this->assertEquals(false, $length->apply(2));
		$this->assertEquals(true, $length->apply(3));
		$this->assertEquals(true, $length->apply(8));
		$this->assertEquals(false, $length->apply(9));

		$length = new Length(8);

		$this->assertEquals(true, $length->apply(2));
		$this->assertEquals(true, $length->apply(3));
		$this->assertEquals(true, $length->apply(8));
		$this->assertEquals(false, $length->apply(9));
	}

	public function testFloatLength()
	{
		$length = new Length(3.4, 8.4);

		$this->assertEquals(false, $length->apply(3.3));
		$this->assertEquals(true, $length->apply(3.4));
		$this->assertEquals(true, $length->apply(8.4));
		$this->assertEquals(false, $length->apply(8.5));

		$length = new Length(8.4);

		$this->assertEquals(true, $length->apply(3.3));
		$this->assertEquals(true, $length->apply(3.4));
		$this->assertEquals(true, $length->apply(8.4));
		$this->assertEquals(false, $length->apply(8.5));
	}

	public function testArrayLength()
	{
		$length = new Length(3, 8);

		$this->assertEquals(false, $length->apply(range(0, 1)));
		$this->assertEquals(true, $length->apply(range(0, 2)));
		$this->assertEquals(true, $length->apply(range(0, 7)));
		$this->assertEquals(false, $length->apply(range(0, 8)));

		$length = new Length(8);

		$this->assertEquals(true, $length->apply(range(0, 1)));
		$this->assertEquals(true, $length->apply(range(0, 2)));
		$this->assertEquals(true, $length->apply(range(0, 7)));
		$this->assertEquals(false, $length->apply(range(0, 8)));
	}

	public function testStringLength()
	{
		$length = new Length(3, 8);

		$this->assertEquals(false, $length->apply('fo'));
		$this->assertEquals(true, $length->apply('foo'));
		$this->assertEquals(true, $length->apply('foobarte'));
		$this->assertEquals(false, $length->apply('foobartes'));

		$length = new Length(8);

		$this->assertEquals(true, $length->apply('fo'));
		$this->assertEquals(true, $length->apply('foo'));
		$this->assertEquals(true, $length->apply('foobarte'));
		$this->assertEquals(false, $length->apply('foobartes'));
	}
}

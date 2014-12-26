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

namespace PSX\Util;

/**
 * CurveArrayTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CurveArrayTest extends \PHPUnit_Framework_TestCase
{
	protected $nestArray;
	protected $flattenArray;

	protected function setUp()
	{
		$this->nestArray = array(
			'id' => null,
			'title' => null,
			'author' => array(
				'name' => null,
				'title' => null,
				'group' => array(
					'id' => null,
					'name' => null,
				),
			),
			'content' => null,
			'date' => null,
		);

		$this->flattenArray = array(
			'id' => null,
			'title' => null,
			'author_name' => null,
			'author_title' => null,
			'author_group_id' => null,
			'author_group_name' => null,
			'content' => null,
			'date' => null,
		);
	}

	public function testNest()
	{
		$this->assertEquals($this->nestArray, CurveArray::nest($this->flattenArray));
	}

	public function testFlatten()
	{
		$this->assertEquals($this->flattenArray, CurveArray::flatten($this->nestArray));
	}

	public function testInverseNest()
	{
		$this->assertEquals($this->nestArray, CurveArray::nest(CurveArray::flatten($this->nestArray)));
	}

	public function testInverseFlatten()
	{
		$this->assertEquals($this->flattenArray, CurveArray::flatten(CurveArray::nest($this->flattenArray)));
	}

	public function testNestWrongInput()
	{
		$data = array(
			'' => null, 
			'_' => null, 
			'__' => null, 
			'___' => null, 
			'foo' => null,
			0 => null, 
		);

		$expect = array(
			0 => null,
			'' => array(
				'' => array(
					'' => array(
					),
				),
			),
			'foo' => null,
		);

		$this->assertEquals($expect, CurveArray::nest($data));
	}

	public function testIsAssoc()
	{
		$this->assertTrue(CurveArray::isAssoc(array()));
		$this->assertFalse(CurveArray::isAssoc(range(0, 9)));
		$this->assertTrue(CurveArray::isAssoc(array('foo' => null, 'bar' => null)));
		$this->assertTrue(CurveArray::isAssoc(array(0 => 'foo', 'foo' => null, 'bar' => null)));
	}
}

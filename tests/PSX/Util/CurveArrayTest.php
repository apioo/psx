<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Util;

/**
 * CurveArrayTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

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

namespace PSX\Data\Schema\Property;

/**
 * ArrayTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ArrayTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = new ArrayType('test');

		$this->assertTrue($property->validate(array()));
		$this->assertTrue($property->validate(array('foo')));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidFormat()
	{
		$property = new ArrayType('test');

		$this->assertTrue($property->validate('foo'));
	}

	public function testValidateNull()
	{
		$property = new ArrayType('test');

		$this->assertTrue($property->validate(null));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateNullIsRequired()
	{
		$property = new ArrayType('test');
		$property->setRequired(true);

		$this->assertTrue($property->validate(null));
		$this->assertTrue($property->isRequired());
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateMinLength()
	{
		$property = new ArrayType('test');
		$property->setMinLength(1);

		$this->assertTrue($property->validate(array()));
		$this->assertEquals(1, $property->getMinLength());
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateMaxLength()
	{
		$property = new ArrayType('test');
		$property->setMaxLength(1);

		$this->assertTrue($property->validate(array('foo', 'bar')));
		$this->assertEquals(1, $property->getMaxLength());
	}

	public function testSetPrototype()
	{
		$prototype = new String('foo');

		$property = new ArrayType('test');
		$property->setPrototype($prototype);

		$this->assertEquals($prototype, $property->getPrototype());
	}
}

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
 * BooleanTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$property = new Boolean('test');

		$this->assertTrue($property->validate(true));
		$this->assertTrue($property->validate(false));
		$this->assertTrue($property->validate(1));
		$this->assertTrue($property->validate(0));
		$this->assertTrue($property->validate('1'));
		$this->assertTrue($property->validate('0'));
		$this->assertTrue($property->validate('true'));
		$this->assertTrue($property->validate('false'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidString()
	{
		$property = new Boolean('test');

		$this->assertTrue($property->validate('foo'));
	}

	/**
	 * @expectedException PSX\Data\Schema\ValidationException
	 */
	public function testValidateInvalidInteger()
	{
		$property = new Boolean('test');

		$this->assertTrue($property->validate(4));
	}

	public function testGetId()
	{
		$property = new Boolean('test');

		$this->assertEquals('b68b84b0d51610192c0f73a5561495b9', $property->getId());
	}
}

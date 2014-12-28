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

namespace PSX\Validate;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Filter;
use PSX\Validate;
use PSX\Validate\Property;

/**
 * ArrayValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ArrayValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$validator->validate(array(
			'id'    => 1,
			'title' => 'foo',
			'date'  => '2013-12-10 00:00:00',
		));
	}

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testValidateFieldNotDefined()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('bar', Validate::TYPE_INTEGER),
		));

		$validator->validate(array(
			'id'    => 1,
			'title' => 'foo',
		));
	}

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testValidateValidationError()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
		));

		$validator->validate(array(
			'id'    => 1,
			'title' => 'foo',
		));
	}

	/**
	 * If the filter returns an string the validate method calls the setter of 
	 * the record to change the value
	 */
	public function testValidateCallSetMethod()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Md5())),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$data = $validator->validate(array(
			'title' => 'foo',
		));

		$this->assertEquals('acbd18db4cc2f85cedef654fccc4a4d8', $data['title']);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testValidateInvalidData()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$validator->validate('foo');
	}

	/**
	 * @expectedException PSX\DisplayException
	 */
	public function testValidateEmptyData()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$validator->validate(array());
	}
}

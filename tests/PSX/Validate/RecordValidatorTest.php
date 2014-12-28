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
 * RecordValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RecordValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$record = new ValidateTestRecord();
		$record->setId(1);
		$record->setTitle('foo');
		$record->setDate('2013-12-10 00:00:00');

		$validator = new RecordValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$validator->validate($record);
	}

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testValidateFieldNotDefined()
	{
		$record = new ValidateTestRecord();
		$record->setId(1);
		$record->setTitle('foo');

		$validator = new RecordValidator(new Validate(), array(
			new Property('bar', Validate::TYPE_INTEGER),
		));

		$validator->validate($record);
	}

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testValidateValidationError()
	{
		$record = new ValidateTestRecord();
		$record->setId(1);
		$record->setTitle('foo');

		$validator = new RecordValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
		));

		$validator->validate($record);
	}

	/**
	 * If the filter returns an string the validate method calls the setter of 
	 * the record to change the value
	 */
	public function testValidateCallSetMethod()
	{
		$record = $this->getMock('PSX\Validate\ValidateTestRecord', array('setTitle'));

		// we must use another method to set the title because setTitle is 
		// already mocked
		$record->setTitleAlt('foo');

		$record->expects($this->once())
			->method('setTitle')
			->with($this->equalTo('acbd18db4cc2f85cedef654fccc4a4d8'));

		$validator = new RecordValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Md5())),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$validator->validate($record);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testValidateInvalidData()
	{
		$validator = new RecordValidator(new Validate(), array(
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
		$validator = new RecordValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$validator->validate(new ValidateTestRecord());
	}
}

class ValidateTestRecord extends RecordAbstract
{
	protected $id;
	protected $title;
	protected $date;

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setDate($date)
	{
		$this->date = $date;
	}
	
	public function getDate()
	{
		return $this->date;
	}

	public function setTitleAlt($title)
	{
		$this->title = $title;
	}
}

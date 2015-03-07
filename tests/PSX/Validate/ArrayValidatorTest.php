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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

		$result = $validator->validate(array(
			'id'    => 1,
			'title' => 'foo',
			'date'  => '2013-12-10 00:00:00',
		));

		$this->assertArrayHasKey('id', $result);
		$this->assertEquals(1, $result['id']);
		$this->assertArrayHasKey('title', $result);
		$this->assertEquals('foo', $result['title']);
		$this->assertArrayHasKey('date', $result);
		$this->assertInstanceOf('DateTime', $result['date']);
		$this->assertEquals('2013-12-10', $result['date']->format('Y-m-d'));
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

	public function testValidateEmptyData()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(2, 8))),
			new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
		));

		$result = $validator->validate(array());

		$this->assertArrayHasKey('id', $result);
		$this->assertEquals(null, $result['id']);
		$this->assertArrayHasKey('title', $result);
		$this->assertEquals(null, $result['title']);
		$this->assertArrayHasKey('date', $result);
		$this->assertEquals(null, $result['date']);
	}
}

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

namespace PSX\Data\Schema\Property;

use PSX\Data\Schema\Property;

/**
 * ChoiceTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testAddElement()
	{
		$complexFoo = Property::getComplex('foo')
			->add(Property::getInteger('foo')->setRequired(true))
			->add(Property::getInteger('bar')->setRequired(true));

		$property = Property::getChoice('foo');
		$property->add($complexFoo);

		$this->assertEquals(['foo' => $complexFoo], $property->getProperties());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testAddElementInvalid()
	{
		Property::getChoice('foo')
			->add(Property::getInteger('foo'));
	}

	public function testGetId()
	{
		$property = Property::getChoice('test');

		$this->assertEquals('1cb150947b4fb85659239644eeafd2fd', $property->getId());

		$complexFoo = Property::getComplex('foo')
			->add(Property::getInteger('foo')->setRequired(true))
			->add(Property::getInteger('bar')->setRequired(true));
		$complexBar = Property::getComplex('bar')
			->add(Property::getInteger('foo')->setRequired(true))
			->add(Property::getInteger('baz')->setRequired(true));

		$property = Property::getChoice('test')->add($complexFoo)->add($complexBar);

		$this->assertEquals('0faf795063796275fc246ec2b8c27929', $property->getId());
	}

	public function testGetTypeName()
	{
		$this->assertEquals('choice', Property::getChoice('test')->getTypeName());
	}
}

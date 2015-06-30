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

namespace PSX\Dependency;

/**
 * ObjectBuilderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectBuilderTest extends \PHPUnit_Framework_TestCase
{
	public function testGetObject()
	{
		$container = new Container();
		$container->set('foo', new \stdClass());
		$container->set('foo_bar', new \DateTime());

		$builder = new ObjectBuilder($container);
		$object  = $builder->getObject('PSX\Dependency\FooService');

		$this->assertInstanceof('PSX\Dependency\FooService', $object);
		$this->assertInstanceof('stdClass', $object->getFoo());
		$this->assertInstanceof('DateTime', $object->getBar());
		$this->assertNull($object->getProperty());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetObjectInjectUnknownService()
	{
		$builder = new ObjectBuilder(new Container());
		$builder->getObject('PSX\Dependency\FooService');
	}

	/**
	 * @expectedException \ReflectionException
	 */
	public function testGetObjectUnknownClass()
	{
		$builder = new ObjectBuilder(new Container());
		$builder->getObject('PSX\Dependency\BarService');
	}

	public function testGetObjectInstanceOf()
	{
		$container = new Container();
		$container->set('foo', new \stdClass());
		$container->set('foo_bar', new \stdClass());

		$builder = new ObjectBuilder($container);
		$object  = $builder->getObject('PSX\Dependency\FooService', array(), 'PSX\Dependency\FooService');

		$this->assertInstanceof('PSX\Dependency\FooService', $object);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetObjectInstanceOfInvalid()
	{
		$container = new Container();
		$container->set('foo', new \stdClass());
		$container->set('foo_bar', new \stdClass());

		$builder = new ObjectBuilder($container);
		$builder->getObject('PSX\Dependency\FooService', array(), 'PSX\Dependency\BarService');
	}

	public function testGetObjectConstructorArguments()
	{
		$container = new Container();
		$container->set('foo', new \stdClass());
		$container->set('foo_bar', new \stdClass());

		$builder = new ObjectBuilder($container);
		$object  = $builder->getObject('PSX\Dependency\FooService', array('foo'), 'PSX\Dependency\FooService');

		$this->assertEquals('foo', $object->getProperty());
	}

	/**
	 * @expectedException \ErrorException
	 */
	public function testGetObjectConstructorArgumentsInvalid()
	{
		$builder = new ObjectBuilder(new Container());
		$builder->getObject('PSX\Dependency\InvalidService');
	}

	public function testGetObjectWithoutConstructor()
	{
		$builder  = new ObjectBuilder(new Container());
		$stdClass = $builder->getObject('stdClass');

		$this->assertInstanceof('stdClass', $stdClass);
	}
}

class FooService
{
	/**
	 * @Inject
	 */
	protected $foo;

	/**
	 * @Inject foo_bar
	 */
	protected $bar;

	protected $property;

	public function __construct($property = null)
	{
		$this->property = $property;
	}

	public function getFoo()
	{
		return $this->foo;
	}

	public function getBar()
	{
		return $this->bar;
	}

	public function getProperty()
	{
		return $this->property;
	}
}

class InvalidService
{
	public function __construct($foo)
	{
	}
}

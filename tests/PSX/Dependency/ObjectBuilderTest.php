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

namespace PSX\Dependency;

/**
 * ObjectBuilderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
	 * @expectedException RuntimeException
	 */
	public function testGetObjectInjectUnknownService()
	{
		$builder = new ObjectBuilder(new Container());
		$builder->getObject('PSX\Dependency\FooService');
	}

	/**
	 * @expectedException ReflectionException
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
	 * @expectedException InvalidArgumentException
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
	 * @expectedException ErrorException
	 */
	public function testGetObjectConstructorArgumentsInvalid()
	{
		$builder = new ObjectBuilder(new Container());
		$builder->getObject('PSX\Dependency\InvalidService');
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

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

namespace PSX\Api;

/**
 * ViewTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructorDefaultStatus()
	{
		$view = new View();

		$this->assertEquals(View::STATUS_ACTIVE, $view->getStatus());
		$this->assertTrue($view->isActive());
		$this->assertFalse($view->isDeprecated());
		$this->assertFalse($view->isClosed());
	}

	public function testConstructorActiveStatus()
	{
		$view = new View(View::STATUS_ACTIVE);

		$this->assertEquals(View::STATUS_ACTIVE, $view->getStatus());
		$this->assertTrue($view->isActive());
		$this->assertFalse($view->isDeprecated());
		$this->assertFalse($view->isClosed());
	}

	public function testConstructorDeprecatedStatus()
	{
		$view = new View(View::STATUS_DEPRECATED);

		$this->assertEquals(View::STATUS_DEPRECATED, $view->getStatus());
		$this->assertFalse($view->isActive());
		$this->assertTrue($view->isDeprecated());
		$this->assertFalse($view->isClosed());
	}

	public function testConstructorClosedStatus()
	{
		$view = new View(View::STATUS_CLOSED);

		$this->assertEquals(View::STATUS_CLOSED, $view->getStatus());
		$this->assertFalse($view->isActive());
		$this->assertFalse($view->isDeprecated());
		$this->assertTrue($view->isClosed());
	}

	public function testSet()
	{
		$view = new View();
		$view->set(1, $this->getDummySchema());

		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->get(1));

		$view->set(1, null);

		$this->assertEmpty($view->get(1));
	}

	public function testIterator()
	{
		$builder = new View\Builder();
		$builder->setGet($this->getDummySchema());
		$builder->setPost($this->getDummySchema(), $this->getDummySchema());
		$builder->setPut($this->getDummySchema(), $this->getDummySchema());
		$builder->setDelete($this->getDummySchema(), $this->getDummySchema());

		$view = $builder->getView();

		foreach($view as $key => $value)
		{
			$this->assertInstanceOf('PSX\Data\SchemaInterface', $value);
		}
	}

	protected function getDummySchema()
	{
		return $this->getMockBuilder('PSX\Data\SchemaInterface')
			->getMock();
	}
}

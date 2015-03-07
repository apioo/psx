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

namespace PSX\Api;

/**
 * ViewTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

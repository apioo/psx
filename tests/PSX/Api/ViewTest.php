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

	public function testGet()
	{
		$view = new View();

		$this->assertFalse($view->hasGet());
		$this->assertFalse($view->hasGetResponse());
		$this->assertEquals(null, $view->getGetResponse());

		$view->setGet($this->getDummySchema());

		$this->assertTrue($view->hasGet());
		$this->assertTrue($view->hasGetResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getGetResponse());
		$this->assertEquals(array('GET'), $view->getAllowedMethods());
	}

	public function testPost()
	{
		$view = new View();

		$this->assertFalse($view->hasPost());
		$this->assertFalse($view->hasPostRequest());
		$this->assertFalse($view->hasPostResponse());
		$this->assertEquals(null, $view->getPostRequest());
		$this->assertEquals(null, $view->getPostResponse());

		$view->setPost($this->getDummySchema(), $this->getDummySchema());

		$this->assertTrue($view->hasPost());
		$this->assertTrue($view->hasPostRequest());
		$this->assertTrue($view->hasPostResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPostRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPostResponse());
		$this->assertEquals(array('POST'), $view->getAllowedMethods());
	}

	public function testPostNoResponse()
	{
		$view = new View();
		$view->setPost($this->getDummySchema());

		$this->assertTrue($view->hasPost());
		$this->assertTrue($view->hasPostRequest());
		$this->assertFalse($view->hasPostResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPostRequest());
		$this->assertEquals(null, $view->getPostResponse());
		$this->assertEquals(array('POST'), $view->getAllowedMethods());
	}

	public function testPut()
	{
		$view = new View();

		$this->assertFalse($view->hasPut());
		$this->assertFalse($view->hasPutRequest());
		$this->assertFalse($view->hasPutResponse());
		$this->assertEquals(null, $view->getPutRequest());
		$this->assertEquals(null, $view->getPutResponse());

		$view->setPut($this->getDummySchema(), $this->getDummySchema());

		$this->assertTrue($view->hasPut());
		$this->assertTrue($view->hasPutRequest());
		$this->assertTrue($view->hasPutResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPutRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPutResponse());
		$this->assertEquals(array('PUT'), $view->getAllowedMethods());
	}

	public function testPutNoResponse()
	{
		$view = new View();
		$view->setPut($this->getDummySchema());

		$this->assertTrue($view->hasPut());
		$this->assertTrue($view->hasPutRequest());
		$this->assertFalse($view->hasPutResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPutRequest());
		$this->assertEquals(null, $view->getPutResponse());
		$this->assertEquals(array('PUT'), $view->getAllowedMethods());
	}

	public function testDelete()
	{
		$view = new View();

		$this->assertFalse($view->hasDelete());
		$this->assertFalse($view->hasDeleteRequest());
		$this->assertFalse($view->hasDeleteResponse());
		$this->assertEquals(null, $view->getDeleteRequest());
		$this->assertEquals(null, $view->getDeleteResponse());

		$view->setDelete($this->getDummySchema(), $this->getDummySchema());

		$this->assertTrue($view->hasDelete());
		$this->assertTrue($view->hasDeleteRequest());
		$this->assertTrue($view->hasDeleteResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getDeleteRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getDeleteResponse());
		$this->assertEquals(array('DELETE'), $view->getAllowedMethods());
	}

	public function testDeleteNoResponse()
	{
		$view = new View();
		$view->setDelete($this->getDummySchema());

		$this->assertTrue($view->hasDelete());
		$this->assertTrue($view->hasDeleteRequest());
		$this->assertFalse($view->hasDeleteResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getDeleteRequest());
		$this->assertEquals(null, $view->getDeleteResponse());
		$this->assertEquals(array('DELETE'), $view->getAllowedMethods());
	}

	public function testSet()
	{
		$view = new View();
		$view->set(1, $this->getDummySchema());

		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->get(1));

		$view->set(1, null);

		$this->assertEmpty($view->get(1));
	}

	public function testGetAllowedMethods()
	{
		$view = new View();
		$view->setGet($this->getDummySchema());
		$view->setPost($this->getDummySchema(), $this->getDummySchema());
		$view->setPut($this->getDummySchema(), $this->getDummySchema());
		$view->setDelete($this->getDummySchema(), $this->getDummySchema());

		$this->assertEquals(array('GET', 'POST', 'PUT', 'DELETE'), $view->getAllowedMethods());
	}

	public function testIterator()
	{
		$view = new View();
		$view->setGet($this->getDummySchema());
		$view->setPost($this->getDummySchema(), $this->getDummySchema());
		$view->setPut($this->getDummySchema(), $this->getDummySchema());
		$view->setDelete($this->getDummySchema(), $this->getDummySchema());

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

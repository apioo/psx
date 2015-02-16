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

namespace PSX\Api\View;

use PSX\Api\View;

/**
 * FacadeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{
	public function testGetAllowedMethods()
	{
		$builder = new Builder();
		$builder->setGet($this->getDummySchema());
		$builder->setPost($this->getDummySchema(), $this->getDummySchema());
		$builder->setPut($this->getDummySchema(), $this->getDummySchema());
		$builder->setDelete($this->getDummySchema(), $this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertEquals(array('GET', 'POST', 'PUT', 'DELETE'), $view->getAllowedMethods());
	}

	public function testGet()
	{
		$view = new Facade(new View());

		$this->assertFalse($view->hasGet());
		$this->assertFalse($view->hasGetResponse());
		$this->assertEquals(null, $view->getGetResponse());

		$builder = new Builder();
		$builder->setGet($this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasGet());
		$this->assertTrue($view->hasGetResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getGetResponse());
		$this->assertEquals(array('GET'), $view->getAllowedMethods());
	}

	public function testPost()
	{
		$view = new Facade(new View());

		$this->assertFalse($view->hasPost());
		$this->assertFalse($view->hasPostRequest());
		$this->assertFalse($view->hasPostResponse());
		$this->assertEquals(null, $view->getPostRequest());
		$this->assertEquals(null, $view->getPostResponse());

		$builder = new Builder();
		$builder->setPost($this->getDummySchema(), $this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasPost());
		$this->assertTrue($view->hasPostRequest());
		$this->assertTrue($view->hasPostResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPostRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPostResponse());
		$this->assertEquals(array('POST'), $view->getAllowedMethods());
	}

	public function testPostNoResponse()
	{
		$builder = new Builder();
		$builder->setPost($this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasPost());
		$this->assertTrue($view->hasPostRequest());
		$this->assertFalse($view->hasPostResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPostRequest());
		$this->assertEquals(null, $view->getPostResponse());
		$this->assertEquals(array('POST'), $view->getAllowedMethods());
	}

	public function testPut()
	{
		$view = new Facade(new View());

		$this->assertFalse($view->hasPut());
		$this->assertFalse($view->hasPutRequest());
		$this->assertFalse($view->hasPutResponse());
		$this->assertEquals(null, $view->getPutRequest());
		$this->assertEquals(null, $view->getPutResponse());

		$builder = new Builder();
		$builder->setPut($this->getDummySchema(), $this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasPut());
		$this->assertTrue($view->hasPutRequest());
		$this->assertTrue($view->hasPutResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPutRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPutResponse());
		$this->assertEquals(array('PUT'), $view->getAllowedMethods());
	}

	public function testPutNoResponse()
	{
		$builder = new Builder();
		$builder->setPut($this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasPut());
		$this->assertTrue($view->hasPutRequest());
		$this->assertFalse($view->hasPutResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getPutRequest());
		$this->assertEquals(null, $view->getPutResponse());
		$this->assertEquals(array('PUT'), $view->getAllowedMethods());
	}

	public function testDelete()
	{
		$view = new Facade(new View());

		$this->assertFalse($view->hasDelete());
		$this->assertFalse($view->hasDeleteRequest());
		$this->assertFalse($view->hasDeleteResponse());
		$this->assertEquals(null, $view->getDeleteRequest());
		$this->assertEquals(null, $view->getDeleteResponse());

		$builder = new Builder();
		$builder->setDelete($this->getDummySchema(), $this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasDelete());
		$this->assertTrue($view->hasDeleteRequest());
		$this->assertTrue($view->hasDeleteResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getDeleteRequest());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getDeleteResponse());
		$this->assertEquals(array('DELETE'), $view->getAllowedMethods());
	}

	public function testDeleteNoResponse()
	{
		$builder = new Builder();
		$builder->setDelete($this->getDummySchema());

		$view = new Facade($builder->getView());

		$this->assertTrue($view->hasDelete());
		$this->assertTrue($view->hasDeleteRequest());
		$this->assertFalse($view->hasDeleteResponse());
		$this->assertInstanceOf('PSX\Data\SchemaInterface', $view->getDeleteRequest());
		$this->assertEquals(null, $view->getDeleteResponse());
		$this->assertEquals(array('DELETE'), $view->getAllowedMethods());
	}

	protected function getDummySchema()
	{
		return $this->getMockBuilder('PSX\Data\SchemaInterface')
			->getMock();
	}
}

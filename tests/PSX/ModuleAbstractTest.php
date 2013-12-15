<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Loader\Location;
use ReflectionClass;

/**
 * ModuleAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ModuleAbstractTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testGetStage()
	{
		$module = $this->getModule();

		$this->assertEquals(0x3F, $module->getStage());
	}

	public function testGetRequestFilter()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->getRequestFilter());
		$this->assertTrue(is_array($module->getRequestFilter()));
	}

	public function testGetResponseFilter()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->getResponseFilter());
		$this->assertTrue(is_array($module->getResponseFilter()));
	}

	public function testOnLoad()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->onLoad());
	}

	public function testOnGet()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->onGet());
	}

	public function testOnPost()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->onPost());
	}

	public function testOnPut()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->onPut());
	}

	public function testOnDelete()
	{
		$module = $this->getModule();

		$this->assertEmpty($module->onDelete());
	}

	public function testProcessResponse()
	{
		$module = $this->getModule();

		$this->assertEquals('foo', $module->processResponse('foo'));
	}

	protected function getModule()
	{
		$container    = getContainer();
		$location     = new Location('foo', '', new ReflectionClass('PSX\TestModule'));
		$basePath     = '';
		$uriFragments = array();

		return new TestModule($container, $location, $basePath, $uriFragments);
	}
}

class TestModule extends ModuleAbstract
{
}

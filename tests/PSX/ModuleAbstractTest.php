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

use PSX\Data\ReaderInterface;
use PSX\Data\Record;
use PSX\Loader\Location;
use PSX\Module\TestModule;
use ReflectionClass;

/**
 * This test set the methods wich are available in an module fix. If we change a
 * method this test will fails. This should make the api more solid since these
 * are the methods wich are used in an application we can not easily change them
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

	public function testGetContainer()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $module->callMethod('getContainer'));
	}

	public function testGetLocation()
	{
		$module   = $this->getModule();
		$location = $module->callMethod('getLocation');

		$this->assertInstanceOf('PSX\Loader\Location', $location);
		$this->assertEquals('foo', $location->getId());
		$this->assertEquals('', $location->getPath());
		$this->assertInstanceOf('ReflectionClass', $location->getClass());
	}

	public function testGetBase()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('PSX\Base', $module->callMethod('getBase'));
	}

	public function testGetBasePath()
	{
		$module = $this->getModule();

		$this->assertEquals('', $module->callMethod('getBasePath'));
	}

	public function testGetUriFragments()
	{
		$module = $this->getModule();

		$this->assertTrue(is_array($module->callMethod('getUriFragments')));
	}

	public function testGetConfig()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('PSX\Config', $module->callMethod('getConfig'));
	}

	public function testGetMethod()
	{
		$module = $this->getModule();

		$this->assertEquals('GET', $module->callMethod('getMethod'));
	}

	public function testGetUrl()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('PSX\Url', $module->callMethod('getUrl'));
	}

	public function testGetHeader()
	{
		$module = $this->getModule();

		$this->assertTrue(is_array($module->callMethod('getHeader')));
	}

	public function testGetParameter()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('PSX\Input', $module->callMethod('getParameter'));
	}

	public function testGetBody()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('PSX\Http\Message', $module->callMethod('getBody', array(ReaderInterface::RAW)));
	}

	public function testImport()
	{
		$record = new Record('foo', array('bar' => 'foo'));
		$module = $this->getModule();

		$module->callMethod('import', array($record, ReaderInterface::FORM));

		$this->assertInstanceOf('PSX\Data\RecordInterface', $record);
	}

	public function testGetRequestReader()
	{
		$module = $this->getModule();

		$this->assertInstanceOf('PSX\Data\Reader\Raw', $module->callMethod('getRequestReader', array(ReaderInterface::RAW)));
	}

	protected function getModule()
	{
		$container    = getContainer();
		$location     = new Location('foo', '', new ReflectionClass('PSX\Module\TestModule'));
		$basePath     = '';
		$uriFragments = array();

		return new TestModule($container, $location, $basePath, $uriFragments);
	}
}

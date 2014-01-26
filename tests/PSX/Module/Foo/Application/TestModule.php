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

namespace PSX\Module\Foo\Application;

use PSX\ModuleAbstract;
use PSX\Data\ReaderInterface;

/**
 * TestModule
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestModule extends ModuleAbstract
{
	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doIndex()
	{
	}

	/**
	 * @httpMethod GET
	 * @path /inspect
	 */
	public function doInspect()
	{
		// inspect inner module API
		$testCase = $this->getTestCase();

		// get container
		$testCase->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->getContainer());

		// get location
		$location = $this->getLocation();

		$testCase->assertInstanceOf('PSX\Loader\Location', $location);
		$testCase->assertInstanceOf('ReflectionClass', $location->getClass());

		// get base
		$testCase->assertInstanceOf('PSX\Base', $this->getBase());

		// get base path
		$testCase->assertEquals('/module/inspect', $this->getBasePath());

		// get uri fragments
		$testCase->assertTrue(is_array($this->getUriFragments()));

		// get config
		$testCase->assertInstanceOf('PSX\Config', $this->getConfig());

		// get method
		$testCase->assertEquals('GET', $this->getMethod());

		// get url
		$testCase->assertInstanceOf('PSX\Url', $this->getUrl());

		// get header
		$testCase->assertTrue(is_array($this->getHeader()));

		// get parameter
		$testCase->assertInstanceOf('PSX\Input', $this->getParameter());

		// get body
		$testCase->assertInstanceOf('PSX\Http\Message', $this->getBody(ReaderInterface::RAW));

		// get request reader
		$testCase->assertInstanceOf('PSX\Data\Reader\Raw', $this->getRequestReader(ReaderInterface::RAW));

		// test properties
		$testCase->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->container);
		$testCase->assertInstanceOf('PSX\Loader\Location', $this->location);
		$testCase->assertEquals('/module/inspect', $this->basePath);
		$testCase->assertTrue(is_array($this->uriFragments));
		$testCase->assertInstanceOf('PSX\Http\Request', $this->request);
		$testCase->assertEquals(0x3F, $this->stage);
		$testCase->assertInstanceOf('PSX\Base', $this->base);
		$testCase->assertInstanceOf('PSX\Config', $this->config);
	}
}

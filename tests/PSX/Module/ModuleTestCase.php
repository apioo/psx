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

namespace PSX\Module;

use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Loader\InvalidPathException;
use PSX\Http\Request;
use PSX\Url;
use ReflectionClass;

/**
 * ModuleTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ModuleTestCase extends \PHPUnit_Framework_TestCase
{
	protected $paths;

	protected function setUp()
	{
		$this->paths = $this->getPaths();
	}

	protected function tearDown()
	{
	}

	public function testGetStage()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEquals(0x3F, $controller->getStage());
	}

	public function testGetRequestFilter()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);
		$filters    = $controller->getRequestFilter();

		$this->assertTrue(is_array($filters));

		foreach($filters as $filter)
		{
			$this->assertInstanceOf('PSX\Dispatch\RequestFilterInterface', $filter);
		}
	}

	public function testGetResponseFilter()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);
		$filters    = $controller->getResponseFilter();

		$this->assertTrue(is_array($filters));

		foreach($filters as $filter)
		{
			$this->assertInstanceOf('PSX\Dispatch\ResponseFilterInterface', $filter);
		}
	}

	public function testOnLoad()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEmpty($controller->onLoad());
	}

	public function testOnGet()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEmpty($controller->onGet());
	}

	public function testOnPost()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEmpty($controller->onPost());
	}

	public function testOnPut()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEmpty($controller->onPut());
	}

	public function testOnDelete()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEmpty($controller->onDelete());
	}

	public function testProcessResponse()
	{
		$path    = key($this->paths) . '/' . __METHOD__;
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');

		$controller = $this->loadModule($path, $request);

		$this->assertEquals('foo', $controller->processResponse('foo'));
	}

	/**
	 * Loads an specific controller
	 *
	 * @param string path
	 * @return PSX\ModuleAbstract
	 */
	protected function loadModule($path, Request $request)
	{
		$availablePaths = $this->paths;

		// set test case for use in the controller
		getContainer()->set('testCase', $this);

		$loader = getContainer()->get('loader');
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($availablePaths, $request){

			$parts    = explode('/', trim($path, '/'));
			$restPath = implode('/', array_slice($parts, 1));

			foreach($availablePaths as $availablePath => $class)
			{
				$availablePath = trim($availablePath, '/');

				if($availablePath == $parts[0])
				{
					return new Location(md5($request->getMethod() . $path), $restPath, new ReflectionClass($class));
				}
			}

			throw new InvalidPathException('Unknown location');

		}));

		$module = $loader->load($path, $request);

		return $module;
	}

	/**
	 * Returns the available modules for the testcase
	 *
	 * @return array
	 */
	abstract protected function getPaths();
}

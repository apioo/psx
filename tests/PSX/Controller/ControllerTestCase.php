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

namespace PSX\Controller;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Loader;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Loader\InvalidPathException;
use PSX\Url;
use ReflectionClass;

/**
 * ControllerTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ControllerTestCase extends \PHPUnit_Framework_TestCase
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
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEquals(0x3F, $controller->getStage());
	}

	public function testGetRequestFilter()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$filters    = $controller->getRequestFilter();

		$this->assertTrue(is_array($filters));

		foreach($filters as $filter)
		{
			$this->assertInstanceOf('PSX\Dispatch\RequestFilterInterface', $filter);
		}
	}

	public function testGetResponseFilter()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$filters    = $controller->getResponseFilter();

		$this->assertTrue(is_array($filters));

		foreach($filters as $filter)
		{
			$this->assertInstanceOf('PSX\Dispatch\ResponseFilterInterface', $filter);
		}
	}

	public function testOnLoad()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEmpty($controller->onLoad());
	}

	public function testOnGet()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEmpty($controller->onGet());
	}

	public function testOnPost()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEmpty($controller->onPost());
	}

	public function testOnPut()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEmpty($controller->onPut());
	}

	public function testOnDelete()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$this->assertEmpty($controller->onDelete());
	}

	public function testProcessResponse()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$path     = key($this->paths) . '/' . __METHOD__;
		$request  = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		$controller->processResponse();
	}

	/**
	 * Loads an specific controller
	 *
	 * @param string path
	 * @return PSX\ModuleAbstract
	 */
	protected function loadController(Request $request, Response $response)
	{
		$availablePaths = $this->paths;

		// set test case for use in the controller
		getContainer()->set('testCase', $this);

		$locationFinder = new CallbackMethod(function($method, $path) use ($availablePaths, $request){

			$parts    = explode('/', trim($path, '/'));
			$restPath = implode('/', array_slice($parts, 1));

			foreach($availablePaths as $availablePath => $class)
			{
				$availablePath = trim($availablePath, '/');

				if($availablePath == $parts[0])
				{
					return new Location(md5($request->getMethod() . $path), $restPath, $class);
				}
			}

			throw new InvalidPathException('Unknown location');

		});

		$loader     = new Loader($locationFinder, getContainer()->get('loader_callback_resolver'));
		$controller = $loader->load($request, $response);

		return $controller;
	}

	/**
	 * Returns the available modules for the testcase
	 *
	 * @return array
	 */
	abstract protected function getPaths();
}

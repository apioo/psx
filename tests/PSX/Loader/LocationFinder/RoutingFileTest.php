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

namespace PSX\Loader\LocationFinder;

use PSX\Loader;
use PSX\Loader\LocationFinder\RoutingFile;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Url;

/**
 * RoutingFileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RoutingFileTest extends \PHPUnit_Framework_TestCase
{
	public function testNormalRoute()
	{
		$module = $this->load('tests/PSX/Loader/routes', '/foo/bar', 'GET');

		$this->assertInstanceOf('PSX\Loader\FooController', $module);

		$module = $this->load('tests/PSX/Loader/routes', '/bar/foo', 'GET');

		$this->assertInstanceOf('PSX\Loader\FooController', $module);

		$module = $this->load('tests/PSX/Loader/routes', '/bar', 'GET');

		$this->assertInstanceOf('PSX\Loader\BarController', $module);

		$module = $this->load('tests/PSX/Loader/routes', '/bar', 'POST');

		$this->assertInstanceOf('PSX\Loader\BarController', $module);
	}

	/**
	 * @expectedException PSX\Loader\InvalidPathException
	 */
	public function testInvalidRoute()
	{
		$module = $this->load('tests/PSX/Loader/routes', '/foo/baz', 'GET');
	}

	/**
	 * @expectedException PSX\Loader\InvalidPathException
	 */
	public function testInvalidRootRoute()
	{
		$module = $this->load('tests/PSX/Loader/routes', '/', 'GET');
	}

	/**
	 * @expectedException PSX\Loader\InvalidPathException
	 */
	public function testInvalidEmptyRoute()
	{
		$module = $this->load('tests/PSX/Loader/routes', '', 'GET');
	}

	public function testWhitespaceRoute()
	{
		$module = $this->load('tests/PSX/Loader/routes', '/whitespace', 'GET');

		$this->assertInstanceOf('PSX\Loader\BarController', $module);
	}

	public function testFallbackRoute()
	{
		$module = $this->load('tests/PSX/Loader/routes_fallback', '/foo/bar', 'GET');

		$this->assertInstanceOf('PSX\Loader\BarController', $module);

		$module = $this->load('tests/PSX/Loader/routes_fallback', '/foo/foo', 'GET');

		$this->assertInstanceOf('PSX\Loader\FooController', $module);

		$module = $this->load('tests/PSX/Loader/routes_fallback', '/foo', 'GET');

		$this->assertInstanceOf('PSX\Loader\BarController', $module);

		$module = $this->load('tests/PSX/Loader/routes_fallback', '/', 'GET');

		$this->assertInstanceOf('PSX\Loader\FooController', $module);

		$module = $this->load('tests/PSX/Loader/routes_fallback', '', 'GET');

		$this->assertInstanceOf('PSX\Loader\FooController', $module);
	}

	protected function load($routingFile, $path, $method, $header = array(), $body = null)
	{
		$loader   = new Loader(new RoutingFile($routingFile), getContainer()->get('loader_callback_resolver'));
		$request  = new Request(new Url('http://127.0.0.1' . $path), $method, $header, $body);
		$response = new Response();
		$module   = $loader->load($request, $response);

		return $module;
	}
}


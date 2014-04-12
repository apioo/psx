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

namespace PSX\Loader\LocationFinder;

use PSX\Loader;
use PSX\Loader\RoutingParser\RoutingFile;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Url;

/**
 * RoutingParserTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RoutingParserTest extends \PHPUnit_Framework_TestCase
{
	public function testNormalRoute()
	{
		$location = $this->resolve('GET', '');
		$this->assertEquals('PSX\Loader\Foo1Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());

		$location = $this->resolve('GET', '/');
		$this->assertEquals('PSX\Loader\Foo1Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());

		$location = $this->resolve('GET', '/foo/bar');
		$this->assertEquals('PSX\Loader\Foo2Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());

		$location = $this->resolve('GET', '/foo/test');
		$this->assertEquals('PSX\Loader\Foo3Controller', $location->getSource());
		$this->assertEquals(array('bar' => 'test'), $location->getParameters());

		$location = $this->resolve('GET', '/foo/test/bar');
		$this->assertEquals('PSX\Loader\Foo4Controller', $location->getSource());
		$this->assertEquals(array('bar' => 'test', 'foo' => 'bar'), $location->getParameters());

		$location = $this->resolve('GET', '/bar');
		$this->assertEquals('PSX\Loader\Foo5Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());

		$location = $this->resolve('GET', '/bar/foo');
		$this->assertEquals('PSX\Loader\Foo6Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());

		$location = $this->resolve('GET', '/bar/14');
		$this->assertEquals('PSX\Loader\Foo7Controller', $location->getSource());
		$this->assertEquals(array('foo' => '14'), $location->getParameters());

		$location = $this->resolve('GET', '/bar/14/16');
		$this->assertEquals('PSX\Loader\Foo8Controller', $location->getSource());
		$this->assertEquals(array('foo' => '14', 'bar' => '16'), $location->getParameters());

		$location = $this->resolve('POST', '/bar');
		$this->assertEquals('PSX\Loader\Foo9Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());

		$location = $this->resolve('GET', '/whitespace');
		$this->assertEquals('PSX\Loader\Foo10Controller', $location->getSource());
		$this->assertEquals(array(), $location->getParameters());
	}

	/**
	 * @expectedException PSX\Loader\InvalidPathException
	 */
	public function testInvalidRoute()
	{
		$this->resolve('/foo/baz', 'GET');
	}

	/**
	 * @expectedException PSX\Loader\InvalidPathException
	 */
	public function testRegexpRoute()
	{
		$this->resolve('GET', '/bar/foo/16');
	}

	protected function resolve($method, $path)
	{
		$locationFinder = new RoutingParser(new RoutingFile('tests/PSX/Loader/routes'));

		return $locationFinder->resolve($method, $path);
	}
}


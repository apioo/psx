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

namespace PSX\Loader;

use PSX\Loader\RoutingParser\RoutingFile;

/**
 * ReverseRouterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ReverseRouterTest extends \PHPUnit_Framework_TestCase
{
	public function testGetPathRoutes()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('/', $router->getPath('PSX\Loader\Foo1Controller'));
		$this->assertEquals('/foo/bar', $router->getPath('PSX\Loader\Foo2Controller'));
		$this->assertEquals('/foo/test', $router->getPath('PSX\Loader\Foo3Controller', array('test')));
		$this->assertEquals('/foo/bla/blub', $router->getPath('PSX\Loader\Foo4Controller', array('bla', 'blub')));
		$this->assertEquals('/bar', $router->getPath('PSX\Loader\Foo5Controller'));
		$this->assertEquals('/bar/foo', $router->getPath('PSX\Loader\Foo6Controller'));
		$this->assertEquals('/bar/12', $router->getPath('PSX\Loader\Foo7Controller', array(12)));
		$this->assertEquals('/bar/37/13', $router->getPath('PSX\Loader\Foo8Controller', array('bar' => 13, 'foo' => 37)));
		$this->assertEquals('/bar', $router->getPath('PSX\Loader\Foo9Controller'));
		$this->assertEquals('/whitespace', $router->getPath('PSX\Loader\Foo10Controller'));
		$this->assertEquals('/test', $router->getPath('PSX\Loader\Foo11Controller'));
		$this->assertEquals('/files/foo/bar', $router->getPath('PSX\Loader\Foo12Controller', array('path' => 'foo/bar')));
		$this->assertEquals('http://cdn.foo.com/files/foo/common.js', $router->getPath('PSX\Loader\Foo13Controller', array('path' => 'foo/common.js')));
	}

	public function testGetPathNamedParameter()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('/foo/bla/blub', $router->getPath('PSX\Loader\Foo4Controller', array('foo' => 'blub', 'bar' => 'bla')));
	}

	public function testGetPathIndexedParameter()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('/foo/bla/blub', $router->getPath('PSX\Loader\Foo4Controller', array('bla', 'blub')));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetPathMissingParameter()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$router->getPath('PSX\Loader\Foo4Controller', array('bla'));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetPathRegExpMissingParameter()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$router->getPath('PSX\Loader\Foo8Controller', array('bla'));
	}

	public function testGetNotExisting()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertNull($router->getPath('Foo\Bar'));
		$this->assertNull($router->getAbsolutePath('Foo\Bar'));
		$this->assertNull($router->getUrl('Foo\Bar'));
	}

	public function testGetPath()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('/foo/bar', $router->getPath('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

		$this->assertEquals('/foo/bar', $router->getPath('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

		$this->assertEquals('/foo/bar', $router->getPath('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('http://cdn.foo.com/files/foo/common.js', $router->getPath('PSX\Loader\Foo13Controller', array('path' => 'foo/common.js')));
	}

	public function testGetAbsolutePath()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('/foo/bar', $router->getAbsolutePath('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

		$this->assertEquals('/foo/bar/foo/bar', $router->getAbsolutePath('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

		$this->assertEquals('/foo/bar/index.php/foo/bar', $router->getAbsolutePath('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('http://cdn.foo.com/files/foo/common.js', $router->getAbsolutePath('PSX\Loader\Foo13Controller', array('path' => 'foo/common.js')));
	}

	public function testGetUrl()
	{
		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('http://foo.com/foo/bar', $router->getUrl('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

		$this->assertEquals('http://foo.com/foo/bar/foo/bar', $router->getUrl('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

		$this->assertEquals('http://foo.com/foo/bar/index.php/foo/bar', $router->getUrl('PSX\Loader\Foo2Controller'));

		$routingFile = new RoutingFile('tests/PSX/Loader/routes');
		$router      = new ReverseRouter($routingFile, 'http://foo.com', '');

		$this->assertEquals('http://cdn.foo.com/files/foo/common.js', $router->getUrl('PSX\Loader\Foo13Controller', array('path' => 'foo/common.js')));
	}
}

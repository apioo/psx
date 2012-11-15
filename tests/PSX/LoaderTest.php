<?php
/*
 *  $Id: LoaderTest.php 658 2012-10-06 22:39:32Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_LoaderTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 658 $
 */
class PSX_LoaderTest extends PHPUnit_Framework_TestCase
{
	private $loader;
	private $path = 'tests/PSX/Loader/module';

	protected function setUp()
	{
		$loader = new PSX_Loader_Test(PSX_Base_Default::getInstance());
		$loader->setPath($this->path);
		$loader->setDefault('foo');
		$loader->setNamespaceStrategy(new PSX_Loader_NamespaceStrategy_Path());

		$this->loader = $loader;
	}

	protected function tearDown()
	{
		unset($this->loader);
	}

	/**
	 * parsePath returns an array with the following values:
	 *
	 * <code>
	 * array(
	 * 	$path, // the path to the class from path_module
	 * 	$file, // the path to the class file
	 * 	$class, // the ReflectionClass of the class
	 * 	$method, // the method wich is called or false
	 * 	$uriFragments, // the uri fragments as array
	 * )
	 * </code>
	 */
	public function testParsePath()
	{
		list($path, $file, $class, $method, $uriFragments) = $this->loader->parsePathPublic('foo');

		$this->assertEquals('', $path);
		$this->assertEquals($this->path . '/foo.php', $file);
		$this->assertEquals(true, $class instanceof ReflectionClass);
		$this->assertEquals('foo', $class->getName());
		$this->assertEquals(false, $method);
		$this->assertEquals(array(), $uriFragments);

		list($path, $file, $class, $method, $uriFragments) = $this->loader->parsePathPublic('foo/test');

		$this->assertEquals('', $path);
		$this->assertEquals($this->path . '/foo.php', $file);
		$this->assertEquals(true, $class instanceof ReflectionClass);
		$this->assertEquals('foo', $class->getName());
		$this->assertEquals(true, $method instanceof ReflectionMethod);
		$this->assertEquals('test', $method->getName());
		$this->assertEquals(array(), $uriFragments);

		list($path, $file, $class, $method, $uriFragments) = $this->loader->parsePathPublic('foo/test/bar');

		$this->assertEquals('', $path);
		$this->assertEquals($this->path . '/foo.php', $file);
		$this->assertEquals(true, $class instanceof ReflectionClass);
		$this->assertEquals('foo', $class->getName());
		$this->assertEquals(true, $method instanceof ReflectionMethod);
		$this->assertEquals('test', $method->getName());
		$this->assertEquals(array('foo' => 'bar'), $uriFragments);
	}

	public function testParsePathExplicit()
	{
		// normal
		$resp   = $this->loader->parsePathPublic('foo/bar');
		$expect = array('', $this->path . '/foo.php', new ReflectionClass('foo'), false, array());

		$this->assertEquals($expect, $resp);

		// leading and trailing slash
		$resp   = $this->loader->parsePathPublic('/foo/bar/');
		$expect = array('', $this->path . '/foo.php', new ReflectionClass('foo'), false, array());

		$this->assertEquals($expect, $resp);

		// method call
		$class  = new ReflectionClass('foo');
		$resp   = $this->loader->parsePathPublic('/foo/test');
		$expect = array('', $this->path . '/foo.php', $class, $class->getMethod('test'), array());

		$this->assertEquals($expect, $resp);
	}

	public function testParsePathInDirExplicit()
	{
		// normal
		$resp   = $this->loader->parsePathPublic('bar/foo/bar');
		$expect = array('bar', $this->path . '/bar/foo.php', new ReflectionClass('bar\foo'), false, array());

		$this->assertEquals($expect, $resp);

		// leading and trailing slash
		$resp   = $this->loader->parsePathPublic('/bar/foo/bar/');
		$expect = array('bar', $this->path . '/bar/foo.php', new ReflectionClass('bar\foo'), false, array());

		$this->assertEquals($expect, $resp);

		// method call
		$class  = new ReflectionClass('bar\foo');
		$resp   = $this->loader->parsePathPublic('/bar/foo/test');
		$expect = array('bar', $this->path . '/bar/foo.php', $class, $class->getMethod('test'), array());

		$this->assertEquals($expect, $resp);
	}

	public function testParsePathInDirDefault()
	{
		// normal
		$resp   = $this->loader->parsePathPublic('bar');
		$expect = array('bar', $this->path . '/bar/index.php', new ReflectionClass('bar\index'), false, array());

		$this->assertEquals($expect, $resp);

		// leading and trailing slash
		$resp   = $this->loader->parsePathPublic('/bar/');
		$expect = array('bar', $this->path . '/bar/index.php', new ReflectionClass('bar\index'), false, array());

		$this->assertEquals($expect, $resp);

		// method call
		$class  = new ReflectionClass('bar\index');
		$resp   = $this->loader->parsePathPublic('/bar/test');
		$expect = array('bar', $this->path . '/bar/index.php', $class, $class->getMethod('test'), array());

		$this->assertEquals($expect, $resp);
	}

	public function testCustomRoutes()
	{
		$this->loader->addRoute('.host-meta/well-known', 'foo');

		$resp   = $this->loader->parsePathPublic('.host-meta/well-known');
		$expect = array('', $this->path . '/foo.php', new ReflectionClass('foo'), false, array());

		$this->assertEquals($expect, $resp);
	}

	public function testGetLocation()
	{
		list($file, $path, $class) = $this->loader->getLocationPublic('foo/some/value');

		$this->assertEquals($this->path . '/foo.php', $file);
		$this->assertEquals('', $path);
		$this->assertEquals('foo', $class);
	}
}

class PSX_Loader_Test extends PSX_Loader
{
	public function parsePathPublic($x)
	{
		return $this->parsePath($x);
	}

	public function getLocationPublic($path)
	{
		return $this->getLocation($path);
	}
}

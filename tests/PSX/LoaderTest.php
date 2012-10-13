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

	protected function setUp()
	{
		$loader = new PSX_Loader_Test(PSX_Base_Default::getInstance());

		$this->loader = $loader;
	}

	protected function tearDown()
	{
		unset($this->loader);
	}

	/**
	 * parsePath path returns an array with the following values
	 *
	 * <code>
	 * array(
	 *
	 * 	$path, // the path to the class from path_module
	 * 	$file, // the path to the class file
	 * 	$class, // the name of the class
	 * 	$method, // the method wich is called or false
	 * 	$uriFragments, // the uri fragments as array
	 *
	 * )
	 * </code>
	 */
	public function testParsePathException()
	{
		$resp   = $this->loader->parsePathPublic('foo/bar');
		$expect = array('sample', 'module/sample.php', new ReflectionClass('sample'), false, array('foo', 'bar'));

		$this->assertEquals($expect, $resp);

		$resp   = $this->loader->parsePathPublic('/foo/bar/');
		$expect = array('sample', 'module/sample.php', new ReflectionClass('sample'), false, array('foo', 'bar'));

		$this->assertEquals($expect, $resp);
	}

	public function testParsePath()
	{
		list($path, $file, $class, $method) = $this->loader->parsePathPublic('sample');

		$this->assertEquals('sample', $path);
		$this->assertEquals(PSX_PATH_MODULE . '/sample.php', $file);
		$this->assertEquals(true, $class instanceof ReflectionClass);
		$this->assertEquals('sample', $class->getName());
		$this->assertEquals(false, $method);

		list($path, $file, $class, $method) = $this->loader->parsePathPublic('sample/foo');

		$this->assertEquals('sample', $path);
		$this->assertEquals(PSX_PATH_MODULE . '/sample.php', $file);
		$this->assertEquals(true, $class instanceof ReflectionClass);
		$this->assertEquals('sample', $class->getName());
		$this->assertEquals(false, $method);

		list($path, $file, $class, $method) = $this->loader->parsePathPublic('sample/foo/bar');

		$this->assertEquals('sample', $path);
		$this->assertEquals(PSX_PATH_MODULE . '/sample.php', $file);
		$this->assertEquals(true, $class instanceof ReflectionClass);
		$this->assertEquals('sample', $class->getName());
		$this->assertEquals(false, $method);
	}

	public function testCustomRoutes()
	{
		$this->loader->addRoute('.host-meta/well-known', 'sample');

		$resp   = $this->loader->parsePathPublic('.host-meta/well-known');
		$expect = array('sample', 'module/sample.php', new ReflectionClass('sample'), false, array('.host-meta', 'well-known'));

		$this->assertEquals($expect, $resp);
	}

	public function testGetPart()
	{
		$part = PSX_Loader::getPart('some/foo/bar');

		$this->assertEquals('some', $part);

		$part = PSX_Loader::getPart('foo/bar');

		$this->assertEquals('foo', $part);

		$part = PSX_Loader::getPart('bar');

		$this->assertEquals('bar', $part);
	}

	public function testGetFPC()
	{
		list($file, $path, $class) = $this->loader->getFPCPublic('sample/some/value');

		$this->assertEquals(PSX_PATH_MODULE . '/sample.php', $file);
		$this->assertEquals('sample', $path);
		$this->assertEquals('sample', $class);
	}
}

class PSX_Loader_Test extends PSX_Loader
{
	public function parsePathPublic($x)
	{
		return $this->parsePath($x);
	}

	public function getFPCPublic($path)
	{
		return $this->getFPC($path);
	}
}

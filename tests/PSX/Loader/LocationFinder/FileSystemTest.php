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

use ReflectionClass;

/**
 * FileSystemTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FileSystemTest extends \PHPUnit_Framework_TestCase
{
	private $finder;
	private $path = 'tests/PSX/Loader/module';

	protected function setUp()
	{
		$this->finder = new FileSystem($this->path);
	}

	protected function tearDown()
	{
		unset($this->finder);
	}

	public function testParsePathExplicit()
	{
		// normal
		$location = $this->finder->resolve('foo/bar');

		$this->assertEquals(new ReflectionClass('foo'), $location->getClass());
		$this->assertEquals('bar', $location->getPath());

		// leading and trailing slash
		$location = $this->finder->resolve('/foo/bar/');

		$this->assertEquals(new ReflectionClass('foo'), $location->getClass());
		$this->assertEquals('bar', $location->getPath());

		// method call
		$location = $this->finder->resolve('/foo/test');

		$this->assertEquals(new ReflectionClass('foo'), $location->getClass());
		$this->assertEquals('test', $location->getPath());

		// long path
		$location = $this->finder->resolve('/foo/lorem/ipsum/path');

		$this->assertEquals(new ReflectionClass('foo'), $location->getClass());
		$this->assertEquals('lorem/ipsum/path', $location->getPath());
	}

	public function testParsePathInDirExplicit()
	{
		// normal
		$location = $this->finder->resolve('bar/foo/bar');

		$this->assertEquals(new ReflectionClass('bar\foo'), $location->getClass());
		$this->assertEquals('bar', $location->getPath());

		// leading and trailing slash
		$location = $this->finder->resolve('/bar/foo/bar/');

		$this->assertEquals(new ReflectionClass('bar\foo'), $location->getClass());
		$this->assertEquals('bar', $location->getPath());

		// method call
		$location = $this->finder->resolve('/bar/foo/test');

		$this->assertEquals(new ReflectionClass('bar\foo'), $location->getClass());
		$this->assertEquals('test', $location->getPath());

		// long path
		$location = $this->finder->resolve('/bar/foo/lorem/ipsum/path');

		$this->assertEquals(new ReflectionClass('bar\foo'), $location->getClass());
		$this->assertEquals('lorem/ipsum/path', $location->getPath());
	}

	public function testParsePathInDirDefault()
	{
		// normal
		$location = $this->finder->resolve('bar');

		$this->assertEquals(new ReflectionClass('bar\index'), $location->getClass());
		$this->assertEquals('', $location->getPath());

		// leading and trailing slash
		$location = $this->finder->resolve('/bar/');

		$this->assertEquals(new ReflectionClass('bar\index'), $location->getClass());
		$this->assertEquals('', $location->getPath());

		// method call
		$location = $this->finder->resolve('/bar/test');

		$this->assertEquals(new ReflectionClass('bar\index'), $location->getClass());
		$this->assertEquals('test', $location->getPath());

		// long path
		$location = $this->finder->resolve('/bar/lorem/ipsum/path');

		$this->assertEquals(new ReflectionClass('bar\index'), $location->getClass());
		$this->assertEquals('lorem/ipsum/path', $location->getPath());
	}

	/**
	 * @expectedException \PSX\Exception
	 */
	public function testMaliciousInput()
	{
		$this->finder->resolve('../../../foo');
	}
}


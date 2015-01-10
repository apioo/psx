<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Loader\RoutingCollection;

/**
 * PathMatcherTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PathMatcherTest extends \PHPUnit_Framework_TestCase
{
	public function testMatch()
	{
		$this->assertMatchTrue('', '');
		$this->assertMatchTrue('', '/');
		$this->assertMatchTrue('foo', 'foo');
		$this->assertMatchTrue('foo', '/foo');
		$this->assertMatchTrue('/foo', 'foo');
		$this->assertMatchTrue('/foo', '/foo');
		$this->assertMatchTrue('/foo/', '/foo/');
		$this->assertMatchTrue('/foo/blub', '/foo/:bar');
		$this->assertMatchTrue('/foo/test', '/foo/:bar');
		$this->assertMatchFalse('/foo', '/foo/:bar');
		$this->assertMatchFalse('/foo/', '/foo/:bar');
		$this->assertMatchTrue('/foo/12', '/foo/$foo<[0-9]+>');
		$this->assertMatchFalse('/foo/test', '/foo/$foo<[0-9]+>');
		$this->assertMatchTrue('/file', '/file/*files');
		$this->assertMatchTrue('/file/', '/file/*files');
		$this->assertMatchTrue('/file/foo', '/file/*files');
		$this->assertMatchTrue('/file/foo/bar', '/file/*files');
		$this->assertMatchTrue('/file/foo/bar/foo.html', '/file/*files');
	}

	protected function assertMatchTrue($srcPath, $destPath)
	{
		$pathMatcher = new PathMatcher($srcPath);
		$result = $pathMatcher->match($destPath);

		$this->assertTrue($result);
	}

	protected function assertMatchFalse($srcPath, $destPath)
	{
		$pathMatcher = new PathMatcher($srcPath);
		$result = $pathMatcher->match($destPath);

		$this->assertFalse($result);
	}
}

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

namespace PSX\Loader\LocationFinder;

use PSX\Loader\Context;
use PSX\Loader\RoutingParser\RoutingFile;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri;

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
		$context = $this->resolve('GET', '');
		$this->assertEquals('PSX\Loader\Foo1Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/');
		$this->assertEquals('PSX\Loader\Foo1Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/foo/bar');
		$this->assertEquals('PSX\Loader\Foo2Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/foo/test');
		$this->assertEquals('PSX\Loader\Foo3Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array('bar' => 'test'), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/foo/test/bar');
		$this->assertEquals('PSX\Loader\Foo4Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array('bar' => 'test', 'foo' => 'bar'), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/bar');
		$this->assertEquals('PSX\Loader\Foo5Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/bar/foo');
		$this->assertEquals('PSX\Loader\Foo6Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/bar/14');
		$this->assertEquals('PSX\Loader\Foo7Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array('foo' => '14'), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/bar/14/16');
		$this->assertEquals('PSX\Loader\Foo8Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array('foo' => '14', 'bar' => '16'), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('POST', '/bar');
		$this->assertEquals('PSX\Loader\Foo9Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/whitespace');
		$this->assertEquals('PSX\Loader\Foo10Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/alias');
		$this->assertEquals('PSX\Loader\Foo2Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array(), $context->get(Context::KEY_FRAGMENT));

		$context = $this->resolve('GET', '/files/foo/bar/foo.htm');
		$this->assertEquals('PSX\Loader\Foo12Controller', $context->get(Context::KEY_SOURCE));
		$this->assertEquals(array('path' => 'foo/bar/foo.htm'), $context->get(Context::KEY_FRAGMENT));
	}

	public function testInvalidRoute()
	{
		$context = $this->resolve('/foo/baz', 'GET');

		$this->assertEquals(null, $context->get(Context::KEY_SOURCE));
		$this->assertEquals(null, $context->get(Context::KEY_FRAGMENT));
	}

	public function testRegexpRoute()
	{
		$context = $this->resolve('GET', '/bar/foo/16');

		$this->assertEquals(null, $context->get(Context::KEY_SOURCE));
		$this->assertEquals(null, $context->get(Context::KEY_FRAGMENT));
	}

	protected function resolve($method, $path)
	{
		$context = new Context();
		$request = new Request(new Uri($path), $method);

		$locationFinder = new RoutingParser(new RoutingFile('tests/PSX/Loader/routes'));
		$locationFinder->resolve($request, $context);

		return $context;
	}
}


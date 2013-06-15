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

namespace PSX;

use PSX\Dispatch;
use PSX\Http\Request;
use PSX\Loader;
use PSX\Loader\Location;
use PSX\ModuleAbstract;
use ReflectionClass;

/**
 * DispatchTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DispatchTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{

	}

	protected function tearDown()
	{
	}

	public function testRoute()
	{
		$loader   = new DummyLoader(getContainer());
		$dispatch = new Dispatch(getContainer()->get('config'), $loader);
		$response = $dispatch->route(new Request(new Url('http://localhost.com'), 'GET'));

		$this->assertEquals('foo', $response->getBody());
	}
}

class DummyLoader extends Loader
{
	public function load($path, Request $request)
	{
		$module = new DummyModule($this->container, new Location('', $path, new ReflectionClass('\PSX\DummyModule')), $path, array());
		$module->onLoad();

		return $module;
	}
}

class DummyModule extends ModuleAbstract
{
	public function onLoad()
	{
		echo 'foo';
	}
}

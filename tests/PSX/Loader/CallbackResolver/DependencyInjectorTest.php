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

namespace PSX\Loader\CallbackResolver;

use PSX\Dispatch\ControllerFactory;
use PSX\Loader\Location;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Url;

/**
 * DependencyInjectorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DependencyInjectorTest extends \PHPUnit_Framework_TestCase
{
	public function testResolve()
	{
		$location = new Location(array(Location::KEY_SOURCE => 'PSX\Loader\RoutingParser\Annotation\BarController::doIndex'));
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$simple   = new DependencyInjector(getContainer()->get('controller_factory'));
		$callback = $simple->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\RoutingParser\Annotation\BarController', $callback->getClass());
		$this->assertEquals('doIndex', $callback->getMethod());
	}

	/**
	 * @expectedException ReflectionException
	 */
	public function testInvalidSource()
	{
		$location = new Location(array(Location::KEY_SOURCE => 'foobar'));
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$simple   = new DependencyInjector(getContainer()->get('controller_factory'));
		$callback = $simple->resolve($location, $request, $response);
	}

	/**
	 * @expectedException ReflectionException
	 */
	public function testClassNotExist()
	{
		$location = new Location(array(Location::KEY_SOURCE => 'Foo::bar'));
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$simple   = new DependencyInjector(getContainer()->get('controller_factory'));
		$callback = $simple->resolve($location, $request, $response);
	}
}

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

namespace PSX\Loader\CallbackResolver;

use PSX\Loader\Location;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Url;

/**
 * AnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
	public function testResolveRootMethod()
	{
		$location = new Location('id', '/', 'PSX\Loader\BarController');
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\BarController', $controller[0]);
		$this->assertEquals('doIndex', $controller[1]);
	}

	public function testResolveMethodPath()
	{
		$location = new Location('id', '/detail/12', 'PSX\Loader\BarController');
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\BarController', $controller[0]);
		$this->assertEquals('doShowDetails', $controller[1]);
	}

	public function testResolveUnknownMethod()
	{
		$location = new Location('id', '/foo', 'PSX\Loader\BarController');
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\BarController', $controller[0]);
		$this->assertEquals('doIndex', $controller[1]);
	}

	public function testResolvePostRequestMethod()
	{
		$location = new Location('id', '/new', 'PSX\Loader\BarController');
		$request  = new Request(new Url('http://127.0.0.1'), 'POST');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\BarController', $controller[0]);
		$this->assertEquals('doInsert', $controller[1]);
	}

	public function testResolvePostUnknownMethod()
	{
		$location = new Location('id', '/', 'PSX\Loader\BarController');
		$request  = new Request(new Url('http://127.0.0.1'), 'POST');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\BarController', $controller[0]);
		$this->assertEquals(null, $controller[1]);
	}

	public function testResolveMethodExplicit()
	{
		$location = new Location('id', '/', 'PSX\Loader\BarController::doShowDetails');
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);

		$this->assertInstanceOf('PSX\Loader\BarController', $controller[0]);
		$this->assertEquals('doShowDetails', $controller[1]);
	}

	/**
	 * @expectedException ReflectionException
	 */
	public function testResolveUnknownClass()
	{
		$location = new Location('id', '/', 'PSX\Loader\UnknownController');
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testResolveUnknownClassExplicit()
	{
		$location = new Location('id', '/', 'PSX\Loader\UnknownController::foo');
		$request  = new Request(new Url('http://127.0.0.1'), 'GET');
		$response = new Response();

		$annotation = new Annotation(getContainer());
		$controller = $annotation->resolve($location, $request, $response);
	}
}

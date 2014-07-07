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

namespace PSX;

use PSX\Dispatch;
use PSX\Dispatch\VoidSender;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Loader;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\ModuleAbstract;
use PSX\Template;

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
		$locationFinder = new CallbackMethod(function($method, $path){

			return new Location(array(Location::KEY_SOURCE => 'PSX\Dispatch\DummyController'));

		});

		$loader   = new Loader($locationFinder, getContainer()->get('loader_callback_resolver'));
		$dispatch = new Dispatch(getContainer()->get('config'), $loader, getContainer()->get('controller_factory'), new VoidSender());
		$request  = new Request(new Url('http://localhost.com'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$dispatch->route($request, $response);

		$this->assertEquals('foo', (string) $response->getBody());
	}

	public function testRouteRedirectException()
	{
		$locationFinder = new CallbackMethod(function($method, $path){

			return new Location(array(Location::KEY_SOURCE => 'PSX\Dispatch\RedirectExceptionController'));

		});

		$loader   = new Loader($locationFinder, getContainer()->get('loader_callback_resolver'));
		$dispatch = new Dispatch(getContainer()->get('config'), $loader, getContainer()->get('controller_factory'), new VoidSender());
		$request  = new Request(new Url('http://localhost.com'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$dispatch->route($request, $response);

		$this->assertEquals(307, $response->getStatusCode());
		$this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
	}
}

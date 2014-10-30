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

use ReflectionClass;
use PSX\Event\RouteMatchedEvent;
use PSX\Event\ControllerExecuteEvent;
use PSX\Event\ControllerProcessedEvent;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Url;

/**
 * LoaderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
	public function testLoadIndexCall()
	{
		$testCase       = $this;
		$locationFinder = new CallbackMethod(function($method, $path) use ($testCase){

			$testCase->assertEquals('/foobar', $path);

			return new Location(array(Location::KEY_SOURCE => 'PSX\Loader\ProbeController::doIndex'));

		});

		// test events
		$routeMatchedListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$routeMatchedListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\RouteMatchedEvent', $event);
				$testCase->assertEquals('GET', $event->getRequestMethod());
				$testCase->assertEquals('/foobar', $event->getPath());
				$testCase->assertInstanceOf('PSX\Loader\Location', $event->getLocation());
				$testCase->assertEquals('PSX\Loader\ProbeController::doIndex', $event->getLocation()->getParameter(Location::KEY_SOURCE));

				return true;
			}));

		$controllerExecuteListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$controllerExecuteListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\ControllerExecuteEvent', $event);
				$testCase->assertInstanceOf('PSX\ControllerInterface', $event->getController());
				$testCase->assertInstanceOf('Psr\Http\Message\RequestInterface', $event->getRequest());
				$testCase->assertInstanceOf('Psr\Http\Message\ResponseInterface', $event->getResponse());

				return true;
			}));

		$controllerProcessedListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$controllerProcessedListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\ControllerProcessedEvent', $event);
				$testCase->assertInstanceOf('PSX\ControllerInterface', $event->getController());
				$testCase->assertInstanceOf('Psr\Http\Message\RequestInterface', $event->getRequest());
				$testCase->assertInstanceOf('Psr\Http\Message\ResponseInterface', $event->getResponse());

				return true;
			}));

		getContainer()->get('event_dispatcher')->addListener(Event::ROUTE_MATCHED, array($routeMatchedListener, 'on'));
		getContainer()->get('event_dispatcher')->addListener(Event::CONTROLLER_EXECUTE, array($controllerExecuteListener, 'on'));
		getContainer()->get('event_dispatcher')->addListener(Event::CONTROLLER_PROCESSED, array($controllerProcessedListener, 'on'));

		$loader   = new Loader($locationFinder, getContainer()->get('loader_callback_resolver'), getContainer()->get('event_dispatcher'));
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();
		$module   = $loader->load($request, $response);

		$expect = array(
			'PSX\Loader\ProbeController::__construct',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::getPreFilter',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::onLoad',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::onGet',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::doIndex',
			'PSX\Loader\ProbeController::processResponse',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::getPostFilter',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());

		getContainer()->get('event_dispatcher')->removeListener(Event::REQUEST_INCOMING, $routeMatchedListener);
		getContainer()->get('event_dispatcher')->removeListener(Event::CONTROLLER_EXECUTE, $controllerExecuteListener);
		getContainer()->get('event_dispatcher')->removeListener(Event::CONTROLLER_PROCESSED, $controllerProcessedListener);
	}

	public function testLoadDetailCall()
	{
		$testCase       = $this;
		$locationFinder = new CallbackMethod(function($method, $path) use ($testCase){

			$testCase->assertEquals('/foobar/detail/12', $path);

			return new Location(array(Location::KEY_SOURCE => 'PSX\Loader\ProbeController::doShowDetails', Location::KEY_FRAGMENT => array('id' => 12)));

		});

		$loader   = new Loader($locationFinder, getContainer()->get('loader_callback_resolver'), getContainer()->get('event_dispatcher'));
		$request  = new Request(new Url('http://127.0.0.1/foobar/detail/12'), 'GET');
		$response = new Response();
		$module   = $loader->load($request, $response);

		$expect = array(
			'PSX\Loader\ProbeController::__construct',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::getPreFilter',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::onLoad',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::onGet',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::doShowDetails',
			'PSX\Loader\ProbeController::processResponse',
			'PSX\Loader\ProbeController::getStage',
			'PSX\Loader\ProbeController::getPostFilter',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
		$this->assertEquals(array('id' => 12), $module->getFragments());
	}
}

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

namespace PSX;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use PSX\Dispatch\Filter\BrowserCache;
use PSX\Event\RouteMatchedEvent;
use PSX\Event\ControllerExecuteEvent;
use PSX\Event\ControllerProcessedEvent;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Loader\Callback;
use PSX\Loader\Context;
use PSX\Loader\FilterController;
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
		$locationFinder = new CallbackMethod(function($request, $context) use ($testCase){

			$testCase->assertEquals('/foobar', $request->getUri()->getPath());

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\ProbeController::doIndex');

			return $request;

		});

		// test events
		$routeMatchedListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$routeMatchedListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\RouteMatchedEvent', $event);
				$testCase->assertInstanceOf('PSX\Http\RequestInterface', $event->getRequest());
				$testCase->assertEquals('GET', $event->getRequest()->getMethod());
				$testCase->assertEquals('/foobar', $event->getRequest()->getUri()->getPath());
				$testCase->assertInstanceOf('PSX\Loader\Context', $event->getContext());
				$testCase->assertEquals('PSX\Loader\ProbeController::doIndex', $event->getContext()->get(Context::KEY_SOURCE));

				return true;
			}));

		$controllerExecuteListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$controllerExecuteListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\ControllerExecuteEvent', $event);
				$testCase->assertInstanceOf('PSX\ControllerInterface', $event->getController());
				$testCase->assertInstanceOf('PSX\Http\RequestInterface', $event->getRequest());
				$testCase->assertInstanceOf('PSX\Http\ResponseInterface', $event->getResponse());

				return true;
			}));

		$controllerProcessedListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$controllerProcessedListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\ControllerProcessedEvent', $event);
				$testCase->assertInstanceOf('PSX\ControllerInterface', $event->getController());
				$testCase->assertInstanceOf('PSX\Http\RequestInterface', $event->getRequest());
				$testCase->assertInstanceOf('PSX\Http\ResponseInterface', $event->getResponse());

				return true;
			}));

		getContainer()->get('event_dispatcher')->addListener(Event::ROUTE_MATCHED, array($routeMatchedListener, 'on'));
		getContainer()->get('event_dispatcher')->addListener(Event::CONTROLLER_EXECUTE, array($controllerExecuteListener, 'on'));
		getContainer()->get('event_dispatcher')->addListener(Event::CONTROLLER_PROCESSED, array($controllerProcessedListener, 'on'));

		$loader   = new Loader(
			$locationFinder, 
			getContainer()->get('loader_callback_resolver'), 
			getContainer()->get('event_dispatcher'), 
			new Logger('psx', [new NullHandler()])
		);
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();
		$module   = $loader->load($request, $response);

		$expect = array(
			'PSX\Loader\ProbeController::__construct',
			'PSX\Loader\ProbeController::getPreFilter',
			'PSX\Loader\ProbeController::getPostFilter',
			'PSX\Loader\ProbeController::onLoad',
			'PSX\Loader\ProbeController::onGet',
			'PSX\Loader\ProbeController::doIndex',
			'PSX\Loader\ProbeController::processResponse',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());

		getContainer()->get('event_dispatcher')->removeListener(Event::REQUEST_INCOMING, $routeMatchedListener);
		getContainer()->get('event_dispatcher')->removeListener(Event::CONTROLLER_EXECUTE, $controllerExecuteListener);
		getContainer()->get('event_dispatcher')->removeListener(Event::CONTROLLER_PROCESSED, $controllerProcessedListener);
	}

	public function testLoadDetailCall()
	{
		$testCase       = $this;
		$locationFinder = new CallbackMethod(function($request, $context) use ($testCase){

			$testCase->assertEquals('/foobar/detail/12', $request->getUri()->getPath());

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\ProbeController::doShowDetails');
			$context->set(Context::KEY_FRAGMENT, array('id' => 12));

			return $request;

		});

		$loader   = new Loader(
			$locationFinder, 
			getContainer()->get('loader_callback_resolver'), 
			getContainer()->get('event_dispatcher'), 
			new Logger('psx', [new NullHandler()])
		);
		$request  = new Request(new Url('http://127.0.0.1/foobar/detail/12'), 'GET');
		$response = new Response();
		$module   = $loader->load($request, $response);

		$expect = array(
			'PSX\Loader\ProbeController::__construct',
			'PSX\Loader\ProbeController::getPreFilter',
			'PSX\Loader\ProbeController::getPostFilter',
			'PSX\Loader\ProbeController::onLoad',
			'PSX\Loader\ProbeController::onGet',
			'PSX\Loader\ProbeController::doShowDetails',
			'PSX\Loader\ProbeController::processResponse',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
		$this->assertEquals(array('id' => 12), $module->getFragments());
	}

	/**
	 * @expectedException PSX\Loader\InvalidPathException
	 */
	public function testLoadUnknownLocation()
	{
		$locationFinder = new CallbackMethod(function($request, $context){

			return null;

		});

		$loader   = new Loader(
			$locationFinder, 
			getContainer()->get('loader_callback_resolver'), 
			getContainer()->get('event_dispatcher'), 
			new Logger('psx', [new NullHandler()])
		);
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$loader->load($request, $response);
	}

	public function testLoadRecursiveOff()
	{
		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'stdClass');

			return $request;

		});

		$controller = new \stdClass();
		$resolver   = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader = $this->getMockBuilder('PSX\Loader')
			->setConstructorArgs(array(
				$locationFinder, 
				$resolver, 
				getContainer()->get('event_dispatcher'), 
				new Logger('psx', [new NullHandler()])
			))
			->setMethods(array('executeController'))
			->getMock();

		$loader->setRecursiveLoading(false);

		$loader->expects($this->once())
			->method('executeController');

		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$this->assertEquals($controller, $loader->load($request, $response));
		$this->assertEquals($controller, $loader->load($request, $response));
	}

	public function testLoadRecursiveOn()
	{
		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'stdClass');

			return $request;

		});

		$controller = new \stdClass();
		$resolver   = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader = $this->getMockBuilder('PSX\Loader')
			->setConstructorArgs(array(
				$locationFinder, 
				$resolver, 
				getContainer()->get('event_dispatcher'),
				new Logger('psx', [new NullHandler()])
			))
			->setMethods(array('executeController'))
			->getMock();

		$loader->setRecursiveLoading(true);

		$loader->expects($this->exactly(2))
			->method('executeController');

		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$this->assertEquals($controller, $loader->load($request, $response));
		$this->assertEquals($controller, $loader->load($request, $response));
	}

	public function testPreFilter()
	{
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\FilterController');

			return $request;

		});

		$filter1 = function($request, $response, $filterChain){
			$filterChain->handle($request, $response);
		};

		$filter2 = $this->getMock('PSX\Dispatch\TestListener');
		$filter2->expects($this->once())
			->method('on')
			->with($request, $response);

		$controller = new FilterController($request, $response);
		$controller->setPreFilter(array($filter1, array($filter2, 'on')));

		$resolver = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader = new Loader(
			$locationFinder, 
			$resolver, 
			getContainer()->get('event_dispatcher'), 
			new Logger('psx', [new NullHandler()])
		);

		$this->assertEquals($controller, $loader->load($request, $response));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testPreFilterInvalid()
	{
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\FilterController');

			return $request;

		});

		$controller = new FilterController($request, $response);
		$controller->setPreFilter(array('foo'));

		$resolver = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader = new Loader(
			$locationFinder, 
			$resolver, 
			getContainer()->get('event_dispatcher'),
			new Logger('psx', [new NullHandler()])
		);
		$loader->load($request, $response);
	}

	public function testPostFilter()
	{
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\FilterController');

			return $request;

		});

		$filter1 = function($request, $response, $filterChain){
			$filterChain->handle($request, $response);
		};

		$filter2 = $this->getMock('PSX\Dispatch\TestListener');
		$filter2->expects($this->once())
			->method('on')
			->with($request, $response);

		$controller = new FilterController($request, $response);
		$controller->setPostFilter(array($filter1, array($filter2, 'on')));

		$resolver = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader = new Loader(
			$locationFinder, 
			$resolver, 
			getContainer()->get('event_dispatcher'),
			new Logger('psx', [new NullHandler()])
		);

		$this->assertEquals($controller, $loader->load($request, $response));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testPostFilterInvalid()
	{
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\FilterController');

			return $request;

		});

		$controller = new FilterController($request, $response);
		$controller->setPostFilter(array('foo'));

		$resolver = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader = new Loader(
			$locationFinder, 
			$resolver, 
			getContainer()->get('event_dispatcher'),
			new Logger('psx', [new NullHandler()])
		);
		$loader->load($request, $response);
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testWrongCallbackClassType()
	{
		$locationFinder = new CallbackMethod(function($request, $context){

			$context->set(Context::KEY_SOURCE, 'PSX\Loader\ProbeController::doIndex');

			return $request;

		});

		$controller = new \stdClass();
		$resolver   = $this->getMock('PSX\Loader\CallbackResolverInterface');

		$resolver
			->method('resolve')
			->will($this->returnValue($controller));

		$loader   = new Loader(
			$locationFinder, 
			$resolver, 
			getContainer()->get('event_dispatcher'),
			new Logger('psx', [new NullHandler()])
		);
		$request  = new Request(new Url('http://127.0.0.1/foobar'), 'GET');
		$response = new Response();

		$loader->load($request, $response);
	}
}

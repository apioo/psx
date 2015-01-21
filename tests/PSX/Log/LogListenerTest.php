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

namespace PSX\Log;

use PSX\DisplayException;
use PSX\Event;
use PSX\Event\RequestIncomingEvent;
use PSX\Event\RouteMatchedEvent;
use PSX\Event\ControllerExecuteEvent;
use PSX\Event\ControllerProcessedEvent;
use PSX\Event\ResponseSendEvent;
use PSX\Event\ExceptionThrownEvent;
use PSX\Event\Context\ControllerContext;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Exception\NotFoundException;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\SeeOtherException;
use PSX\Uri;
use PSX\Loader\Location;
use PSX\Test\ControllerTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * LogListenerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LogListenerTest extends ControllerTestCase
{
	public function testEventRequestIncomming()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('info')
			->with($this->equalTo('Incoming request GET /foo.htm'));

		$request = new Request(new Uri('/foo.htm'), 'GET');

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));
		$eventDispatcher->dispatch(Event::REQUEST_INCOMING, new RequestIncomingEvent($request));
	}

	public function testEventRouteMatched()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('info')
			->with($this->equalTo('Route matched GET /foo.htm -> stdClass'));

		$location = new Location();
		$location->setParameter(Location::KEY_SOURCE, 'stdClass');

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));
		$eventDispatcher->dispatch(Event::ROUTE_MATCHED, new RouteMatchedEvent('GET', '/foo.htm', $location));
	}

	public function testEventControllerExecute()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('info')
			->with($this->equalTo('Controller execute PSX\Controller\Foo\Application\TestController'));

		$location   = new Location();
		$request    = new Request(new Uri('/foo.htm'), 'GET');
		$response   = new Response();
		$controller = getContainer()->getControllerFactory()->getController('PSX\Controller\Foo\Application\TestController', $location, $request, $response);

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));
		$eventDispatcher->dispatch(Event::CONTROLLER_EXECUTE, new ControllerExecuteEvent($controller, $request, $response));
	}

	public function testEventControllerProcessed()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('info')
			->with($this->equalTo('Controller processed PSX\Controller\Foo\Application\TestController'));

		$location   = new Location();
		$request    = new Request(new Uri('/foo.htm'), 'GET');
		$response   = new Response();
		$controller = getContainer()->getControllerFactory()->getController('PSX\Controller\Foo\Application\TestController', $location, $request, $response);

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));
		$eventDispatcher->dispatch(Event::CONTROLLER_PROCESSED, new ControllerProcessedEvent($controller, $request, $response));
	}

	public function testEventResponseSend()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('info')
			->with($this->equalTo('Send response'));

		$response = new Response();

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));
		$eventDispatcher->dispatch(Event::RESPONSE_SEND, new ResponseSendEvent($response));
	}

	public function testEventExceptionThrownDisplayException()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('notice')
			->with($this->equalTo('foobar'));

		$request  = new Request(new Uri('/foo.htm'), 'GET');
		$response = new Response();

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));

		try
		{
			throw new DisplayException('foobar');
		}
		catch(DisplayException $e)
		{
			$eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
		}
	}

	public function testEventExceptionThrownException()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('error')
			->with($this->equalTo('foobar'));

		$request  = new Request(new Uri('/foo.htm'), 'GET');
		$response = new Response();

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));

		try
		{
			throw new \Exception('foobar');
		}
		catch(\Exception $e)
		{
			$eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
		}
	}

	public function testEventExceptionThrownStatusCodeExceptionClientError()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('notice')
			->with($this->equalTo('foobar'));

		$request  = new Request(new Uri('/foo.htm'), 'GET');
		$response = new Response();

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));

		try
		{
			throw new NotFoundException('foobar');
		}
		catch(NotFoundException $e)
		{
			$eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
		}
	}

	public function testEventExceptionThrownStatusCodeExceptionServerError()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('error')
			->with($this->equalTo('foobar'));

		$request  = new Request(new Uri('/foo.htm'), 'GET');
		$response = new Response();

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));

		try
		{
			throw new InternalServerErrorException('foobar');
		}
		catch(InternalServerErrorException $e)
		{
			$eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
		}
	}

	public function testEventExceptionThrownStatusCodeExceptionSeeOther()
	{
		$logger = $this->getLogger();
		$logger->expects($this->once())
			->method('info')
			->with($this->equalTo('Redirect exception'));

		$request  = new Request(new Uri('/foo.htm'), 'GET');
		$response = new Response();

		$eventDispatcher = new EventDispatcher();
		$eventDispatcher->addSubscriber(new LogListener($logger));

		try
		{
			throw new SeeOtherException('/bar.htm');
		}
		catch(SeeOtherException $e)
		{
			$eventDispatcher->dispatch(Event::EXCEPTION_THROWN, new ExceptionThrownEvent($e, new ControllerContext($request, $response)));
		}
	}

	protected function getLogger()
	{
		return $this->getMock('Psr\Log\LoggerInterface', array('emergency', 'alert', 'critical', 'warning', 'debug', 'log', 'info', 'notice', 'error'));
	}

	protected function getPaths()
	{
		return array();
	}
}
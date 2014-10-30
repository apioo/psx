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
use PSX\Dispatch\Sender\Void as VoidSender;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Loader;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\ModuleAbstract;
use PSX\Template;
use PSX\Test\ControllerTestCase;

/**
 * DispatchTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DispatchTest extends ControllerTestCase
{
	public function testRoute()
	{
		$request  = new Request(new Url('http://localhost.com/dummy'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals('foo', (string) $response->getBody());
	}

	public function testRouteRedirectException()
	{
		$request  = new Request(new Url('http://localhost.com/redirect'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(307, $response->getStatusCode());
		$this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
	}

	public function testRouteException()
	{
		$request  = new Request(new Url('http://localhost.com/exception'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(500, $response->getStatusCode());
	}

	public function testRouteStatusCodeException()
	{
		$request  = new Request(new Url('http://localhost.com/exception_code'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(501, $response->getStatusCode());
	}

	public function testRouteWrongStatusCodeException()
	{
		$request  = new Request(new Url('http://localhost.com/exception_wrong_code'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(500, $response->getStatusCode());
	}

	public function testBadRequestException()
	{
		$request  = new Request(new Url('http://localhost.com/400'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(400, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testConflictException()
	{
		$request  = new Request(new Url('http://localhost.com/409'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(409, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testForbiddenException()
	{
		$request  = new Request(new Url('http://localhost.com/403'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(403, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testFoundException()
	{
		$request  = new Request(new Url('http://localhost.com/302'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(302, $response->getStatusCode());
		$this->assertEquals('http://google.com', $response->getHeader('Location'));
		$this->assertEquals(null, $response->getBody());
	}

	public function testGoneException()
	{
		$request  = new Request(new Url('http://localhost.com/410'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(410, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testInternalServerErrorException()
	{
		$request  = new Request(new Url('http://localhost.com/500'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(500, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testMethodNotAllowedException()
	{
		$request  = new Request(new Url('http://localhost.com/405'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(405, $response->getStatusCode());
		$this->assertEquals('GET, POST', $response->getHeader('Allow'));
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testMovedPermanentlyException()
	{
		$request  = new Request(new Url('http://localhost.com/301'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(301, $response->getStatusCode());
		$this->assertEquals('http://google.com', $response->getHeader('Location'));
		$this->assertEquals(null, $response->getBody());
	}

	public function testNotAcceptableException()
	{
		$request  = new Request(new Url('http://localhost.com/406'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(406, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testNotFoundException()
	{
		$request  = new Request(new Url('http://localhost.com/404'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(404, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testNotImplementedException()
	{
		$request  = new Request(new Url('http://localhost.com/501'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(501, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testNotModifiedException()
	{
		$request  = new Request(new Url('http://localhost.com/304'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(304, $response->getStatusCode());
		$this->assertEquals(null, $response->getBody());
	}

	public function testSeeOtherException()
	{
		$request  = new Request(new Url('http://localhost.com/303'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(303, $response->getStatusCode());
		$this->assertEquals('http://google.com', $response->getHeader('Location'));
		$this->assertEquals(null, $response->getBody());
	}

	public function testServiceUnavailableException()
	{
		$request  = new Request(new Url('http://localhost.com/503'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(503, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testTemporaryRedirectException()
	{
		$request  = new Request(new Url('http://localhost.com/307'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(307, $response->getStatusCode());
		$this->assertEquals('http://google.com', $response->getHeader('Location'));
		$this->assertEquals(null, $response->getBody());
	}

	public function testUnauthorizedException()
	{
		$request  = new Request(new Url('http://localhost.com/401'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(401, $response->getStatusCode());
		$this->assertEquals('Basic realm="foo"', $response->getHeader('WWW-Authenticate'));
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	public function testUnsupportedMediaTypeException()
	{
		$request  = new Request(new Url('http://localhost.com/415'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$this->loadController($request, $response);

		$this->assertEquals(415, $response->getStatusCode());
		$this->assertInstanceOf('PSX\Http\Stream\StringStream', $response->getBody());
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/dummy', 'PSX\Dispatch\DummyController'],
			[['GET'], '/redirect', 'PSX\Dispatch\RedirectExceptionController'],
			[['GET'], '/exception', 'PSX\Dispatch\ExceptionController::doException'],
			[['GET'], '/exception_code', 'PSX\Dispatch\ExceptionController::doStatusCodeException'],
			[['GET'], '/exception_wrong_code', 'PSX\Dispatch\ExceptionController::doWrongStatusCodeException'],
			[['GET'], '/400', 'PSX\Dispatch\StatusCodeExceptionController::throwBadRequestException'],
			[['GET'], '/409', 'PSX\Dispatch\StatusCodeExceptionController::throwConflictException'],
			[['GET'], '/403', 'PSX\Dispatch\StatusCodeExceptionController::throwForbiddenException'],
			[['GET'], '/302', 'PSX\Dispatch\StatusCodeExceptionController::throwFoundException'],
			[['GET'], '/410', 'PSX\Dispatch\StatusCodeExceptionController::throwGoneException'],
			[['GET'], '/500', 'PSX\Dispatch\StatusCodeExceptionController::throwInternalServerErrorException'],
			[['GET'], '/405', 'PSX\Dispatch\StatusCodeExceptionController::throwMethodNotAllowedException'],
			[['GET'], '/301', 'PSX\Dispatch\StatusCodeExceptionController::throwMovedPermanentlyException'],
			[['GET'], '/406', 'PSX\Dispatch\StatusCodeExceptionController::throwNotAcceptableException'],
			[['GET'], '/404', 'PSX\Dispatch\StatusCodeExceptionController::throwNotFoundException'],
			[['GET'], '/501', 'PSX\Dispatch\StatusCodeExceptionController::throwNotImplementedException'],
			[['GET'], '/304', 'PSX\Dispatch\StatusCodeExceptionController::throwNotModifiedException'],
			[['GET'], '/303', 'PSX\Dispatch\StatusCodeExceptionController::throwSeeOtherException'],
			[['GET'], '/503', 'PSX\Dispatch\StatusCodeExceptionController::throwServiceUnavailableException'],
			[['GET'], '/307', 'PSX\Dispatch\StatusCodeExceptionController::throwTemporaryRedirectException'],
			[['GET'], '/401', 'PSX\Dispatch\StatusCodeExceptionController::throwUnauthorizedException'],
			[['GET'], '/415', 'PSX\Dispatch\StatusCodeExceptionController::throwUnsupportedMediaTypeException'],
		);
	}
}

<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;

/**
 * DispatchTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DispatchTest extends ControllerTestCase
{
    public function testRoute()
    {
        $testCase = $this;

        $requestIncomingListener = $this->getMock('PSX\Framework\Tests\Dispatch\TestListener', array('on'));
        $requestIncomingListener->expects($this->once())
            ->method('on')
            ->with($this->callback(function (RequestIncomingEvent $event) use ($testCase) {
                $testCase->assertInstanceOf('PSX\Framework\Event\RequestIncomingEvent', $event);
                $testCase->assertInstanceOf('PSX\Http\RequestInterface', $event->getRequest());

                return true;
            }));

        $responseSendListener = $this->getMock('PSX\Framework\Tests\Dispatch\TestListener', array('on'));
        $responseSendListener->expects($this->once())
            ->method('on')
            ->with($this->callback(function (ResponseSendEvent $event) use ($testCase) {
                $testCase->assertInstanceOf('PSX\Framework\Event\ResponseSendEvent', $event);
                $testCase->assertInstanceOf('PSX\Http\ResponseInterface', $event->getResponse());

                return true;
            }));

        Environment::getService('event_dispatcher')->addListener(Event::REQUEST_INCOMING, array($requestIncomingListener, 'on'));
        Environment::getService('event_dispatcher')->addListener(Event::RESPONSE_SEND, array($responseSendListener, 'on'));

        $response = $this->sendRequest('http://localhost.com/dummy', 'GET');

        Environment::getService('event_dispatcher')->removeListener(Event::REQUEST_INCOMING, $requestIncomingListener);
        Environment::getService('event_dispatcher')->removeListener(Event::RESPONSE_SEND, $responseSendListener);

        $this->assertEquals('foo', (string) $response->getBody());
    }

    public function testRouteRedirectException()
    {
        $response = $this->sendRequest('http://localhost.com/redirect', 'GET');

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://localhost.com/foobar', $response->getHeader('Location'));
    }

    public function testRouteException()
    {
        $testCase = $this;

        $exceptionListener = $this->getMock('PSX\Framework\Tests\Dispatch\TestListener', array('on'));
        $exceptionListener->expects($this->once())
            ->method('on')
            ->with($this->callback(function (ExceptionThrownEvent $event) use ($testCase) {
                $testCase->assertInstanceOf('PSX\Framework\Event\ExceptionThrownEvent', $event);
                $testCase->assertInstanceOf('PSX\Framework\Event\Context\ControllerContext', $event->getContext());
                $testCase->assertInstanceOf('PSX\Http\RequestInterface', $event->getContext()->getRequest());
                $testCase->assertInstanceOf('PSX\Http\ResponseInterface', $event->getContext()->getResponse());

                return true;
            }));

        Environment::getService('event_dispatcher')->addListener(Event::EXCEPTION_THROWN, array($exceptionListener, 'on'));

        $response = $this->sendRequest('http://localhost.com/exception', 'GET');

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testRouteStatusCodeException()
    {
        $response = $this->sendRequest('http://localhost.com/exception_code', 'GET');

        $this->assertEquals(501, $response->getStatusCode());
    }

    public function testRouteWrongStatusCodeException()
    {
        $response = $this->sendRequest('http://localhost.com/exception_wrong_code', 'GET');

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testBadRequestException()
    {
        $response = $this->sendRequest('http://localhost.com/400', 'GET');

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testConflictException()
    {
        $response = $this->sendRequest('http://localhost.com/409', 'GET');

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testForbiddenException()
    {
        $response = $this->sendRequest('http://localhost.com/403', 'GET');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testFoundException()
    {
        $response = $this->sendRequest('http://localhost.com/302', 'GET');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://google.com', $response->getHeader('Location'));
        $this->assertEquals('', (string) $response->getBody());
    }

    public function testGoneException()
    {
        $response = $this->sendRequest('http://localhost.com/410', 'GET');

        $this->assertEquals(410, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testInternalServerErrorException()
    {
        $response = $this->sendRequest('http://localhost.com/500', 'GET');

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testMethodNotAllowedException()
    {
        $response = $this->sendRequest('http://localhost.com/405', 'GET');

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('GET, POST', $response->getHeader('Allow'));
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testMovedPermanentlyException()
    {
        $response = $this->sendRequest('http://localhost.com/301', 'GET');

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('http://google.com', $response->getHeader('Location'));
        $this->assertEquals('', (string) $response->getBody());
    }

    public function testNotAcceptableException()
    {
        $response = $this->sendRequest('http://localhost.com/406', 'GET');

        $this->assertEquals(406, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testNotFoundException()
    {
        $response = $this->sendRequest('http://localhost.com/404', 'GET');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testNotImplementedException()
    {
        $response = $this->sendRequest('http://localhost.com/501', 'GET');

        $this->assertEquals(501, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testNotModifiedException()
    {
        $response = $this->sendRequest('http://localhost.com/304', 'GET');

        $this->assertEquals(304, $response->getStatusCode());
        $this->assertEquals('', (string) $response->getBody());
    }

    public function testSeeOtherException()
    {
        $response = $this->sendRequest('http://localhost.com/303', 'GET');

        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals('http://google.com', $response->getHeader('Location'));
        $this->assertEquals('', (string) $response->getBody());
    }

    public function testServiceUnavailableException()
    {
        $response = $this->sendRequest('http://localhost.com/503', 'GET');

        $this->assertEquals(503, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testTemporaryRedirectException()
    {
        $response = $this->sendRequest('http://localhost.com/307', 'GET');

        $this->assertEquals(307, $response->getStatusCode());
        $this->assertEquals('http://google.com', $response->getHeader('Location'));
        $this->assertEquals('', (string) $response->getBody());
    }

    public function testUnauthorizedException()
    {
        $response = $this->sendRequest('http://localhost.com/401', 'GET');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Basic realm="foo"', $response->getHeader('WWW-Authenticate'));
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testUnauthorizedNoParameterException()
    {
        $response = $this->sendRequest('http://localhost.com/401_1', 'GET');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Foo', $response->getHeader('WWW-Authenticate'));
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    public function testUnsupportedMediaTypeException()
    {
        $response = $this->sendRequest('http://localhost.com/415', 'GET');

        $this->assertEquals(415, $response->getStatusCode());
        $this->assertInstanceOf('PSX\Http\Stream\TempStream', $response->getBody());
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/dummy', 'PSX\Framework\Tests\Dispatch\DummyController'],
            [['GET'], '/redirect', 'PSX\Framework\Tests\Dispatch\RedirectExceptionController'],
            [['GET'], '/exception', 'PSX\Framework\Tests\Dispatch\ExceptionController::doException'],
            [['GET'], '/exception_code', 'PSX\Framework\Tests\Dispatch\ExceptionController::doStatusCodeException'],
            [['GET'], '/exception_wrong_code', 'PSX\Framework\Tests\Dispatch\ExceptionController::doWrongStatusCodeException'],
            [['GET'], '/400', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwBadRequestException'],
            [['GET'], '/409', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwConflictException'],
            [['GET'], '/403', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwForbiddenException'],
            [['GET'], '/302', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwFoundException'],
            [['GET'], '/410', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwGoneException'],
            [['GET'], '/500', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwInternalServerErrorException'],
            [['GET'], '/405', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwMethodNotAllowedException'],
            [['GET'], '/301', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwMovedPermanentlyException'],
            [['GET'], '/406', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwNotAcceptableException'],
            [['GET'], '/404', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwNotFoundException'],
            [['GET'], '/501', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwNotImplementedException'],
            [['GET'], '/304', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwNotModifiedException'],
            [['GET'], '/303', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwSeeOtherException'],
            [['GET'], '/503', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwServiceUnavailableException'],
            [['GET'], '/307', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwTemporaryRedirectException'],
            [['GET'], '/401', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwUnauthorizedException'],
            [['GET'], '/401_1', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwUnauthorizedNoParameterException'],
            [['GET'], '/415', 'PSX\Framework\Tests\Dispatch\StatusCodeExceptionController::throwUnsupportedMediaTypeException'],
        );
    }
}

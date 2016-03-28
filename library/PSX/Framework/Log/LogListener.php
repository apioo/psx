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

namespace PSX\Framework\Log;

use Psr\Log\LoggerInterface;
use PSX\Framework\DisplayException;
use PSX\Framework\Event\ControllerExecuteEvent;
use PSX\Framework\Event\ControllerProcessedEvent;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\ExceptionThrownEvent;
use PSX\Framework\Event\RequestIncomingEvent;
use PSX\Framework\Event\ResponseSendEvent;
use PSX\Framework\Event\RouteMatchedEvent;
use PSX\Framework\Loader\Context;
use PSX\Http\Exception\StatusCodeException;
use PSX\Http\Http;
use PSX\Http\Stream\Util;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * LogListener
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LogListener implements EventSubscriberInterface
{
    protected $logger;
    protected $debug;

    public function __construct(LoggerInterface $logger, $debug = false)
    {
        $this->logger = $logger;
        $this->debug  = $debug;
    }

    public function onRequestIncomming(RequestIncomingEvent $event)
    {
        $this->logger->info('Incoming request ' . $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getRequestTarget());

        if ($this->debug) {
            $body = Util::toString($event->getRequest()->getBody());
            if (!empty($body)) {
                $this->logger->debug($body);
            }
        }
    }

    public function onRouteMatched(RouteMatchedEvent $event)
    {
        $request    = $event->getRequest();
        $path       = '/' . ltrim($request->getUri()->getPath(), '/');
        $controller = $event->getContext()->get(Context::KEY_SOURCE);

        $this->logger->info('Route matched ' . $request->getMethod() . ' ' . $path . ' -> ' . $controller);
    }

    public function onControllerExecute(ControllerExecuteEvent $event)
    {
        $controller = get_class($event->getController());

        $this->logger->info('Controller execute ' . $controller);
    }

    public function onControllerProcessed(ControllerProcessedEvent $event)
    {
        $controller = get_class($event->getController());

        $this->logger->info('Controller processed ' . $controller);
    }

    public function onResponseSend(ResponseSendEvent $event)
    {
        $code = $event->getResponse()->getStatusCode();
        $code = isset(Http::$codes[$code]) ? $code : 200;

        $this->logger->info('Send response ' . $code);

        if ($this->debug) {
            $body = Util::toString($event->getResponse()->getBody());
            if (!empty($body)) {
                $this->logger->debug($body);
            }
        }
    }

    public function onExceptionThrown(ExceptionThrownEvent $event)
    {
        $exception = $event->getException();
        $severity  = $exception instanceof \ErrorException ? $exception->getSeverity() : null;
        $context   = array(
            'file'     => $exception->getFile(),
            'line'     => $exception->getLine(),
            'trace'    => $exception->getTraceAsString(),
            'code'     => $exception->getCode(),
            'severity' => $severity,
        );

        if ($exception instanceof StatusCodeException) {
            if ($exception->isClientError()) {
                $this->logger->notice($exception->getMessage(), $context);
            } elseif ($exception->isServerError()) {
                $this->logger->error($exception->getMessage(), $context);
            } else {
                $this->logger->info($exception->getMessage(), $context);
            }
        } elseif ($exception instanceof DisplayException) {
            $this->logger->notice($exception->getMessage(), $context);
        } else {
            $this->logger->error($exception->getMessage(), $context);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            Event::REQUEST_INCOMING     => 'onRequestIncomming',
            Event::ROUTE_MATCHED        => 'onRouteMatched',
            Event::CONTROLLER_EXECUTE   => 'onControllerExecute',
            Event::CONTROLLER_PROCESSED => 'onControllerProcessed',
            Event::RESPONSE_SEND        => 'onResponseSend',
            Event::EXCEPTION_THROWN     => 'onExceptionThrown',
        );
    }
}

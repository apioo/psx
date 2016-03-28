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

namespace PSX\Framework\Loader;

use Closure;
use Psr\Log\LoggerInterface;
use PSX\Framework\ApplicationStackInterface;
use PSX\Framework\Config\Config;
use PSX\Framework\Dependency\ObjectBuilder;
use PSX\Framework\Filter\FilterChain;
use PSX\Framework\Filter\FilterInterface;
use PSX\Framework\Event\ControllerExecuteEvent;
use PSX\Framework\Event\ControllerProcessedEvent;
use PSX\Framework\Event\Event;
use PSX\Framework\Event\RouteMatchedEvent;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UnexpectedValueException;

/**
 * Loader
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Loader implements LoaderInterface
{
    /**
     * @var \PSX\Framework\Loader\LocationFinderInterface
     */
    protected $locationFinder;

    /**
     * @var \PSX\Framework\Loader\CallbackResolverInterface
     */
    protected $callbackResolver;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \PSX\Framework\Dependency\ObjectBuilder
     */
    protected $objectBuilder;

    /**
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @var boolean
     */
    protected $recursiveLoading = false;

    /**
     * @var array
     */
    protected $loaded = array();

    public function __construct(LocationFinderInterface $locationFinder, CallbackResolverInterface $callbackResolver, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger, ObjectBuilder $objectBuilder, Config $config)
    {
        $this->locationFinder   = $locationFinder;
        $this->callbackResolver = $callbackResolver;
        $this->eventDispatcher  = $eventDispatcher;
        $this->logger           = $logger;
        $this->objectBuilder    = $objectBuilder;
        $this->config           = $config;
    }

    public function setRecursiveLoading($recursiveLoading)
    {
        $this->recursiveLoading = $recursiveLoading;
    }

    /**
     * Loads the location of the controller through the defined location finder.
     * Then uses the callback resolver to obtain the controller. After this the
     * controller gets executed
     *
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @param \PSX\Framework\Loader\Context $context
     * @return mixed
     */
    public function load(RequestInterface $request, ResponseInterface $response, Context $context = null)
    {
        $context = $context ?: new Context();
        $result  = $this->locationFinder->resolve($request, $context);

        if ($result instanceof RequestInterface) {
            $this->eventDispatcher->dispatch(Event::ROUTE_MATCHED, new RouteMatchedEvent($result, $context));

            $controller = $this->callbackResolver->resolve($result, $response, $context);
            $id         = spl_object_hash($controller);

            if ($this->recursiveLoading || !in_array($id, $this->loaded)) {
                $this->executeController($controller, $result, $response);

                $this->loaded[] = $id;
            }

            return $controller;
        } else {
            throw new InvalidPathException('Unkown location', $request);
        }
    }

    /**
     * Executes an specific controller direct without any routing
     *
     * @param mixed $controller
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Http\ResponseInterface $response
     * @return void
     */
    public function executeController($controller, RequestInterface $request, ResponseInterface $response)
    {
        if ($controller instanceof ApplicationStackInterface) {
            $this->eventDispatcher->dispatch(Event::CONTROLLER_EXECUTE, new ControllerExecuteEvent($controller, $request, $response));

            $filters = array_merge(
                $this->resolveFilters($this->config->get('psx_filter_pre')),
                $controller->getApplicationStack(),
                $this->resolveFilters($this->config->get('psx_filter_post'))
            );

            $filterChain = new FilterChain($filters);
            $filterChain->setLogger($this->logger);
            $filterChain->handle($request, $response);

            $this->eventDispatcher->dispatch(Event::CONTROLLER_PROCESSED, new ControllerProcessedEvent($controller, $request, $response));
        } else {
            throw new UnexpectedValueException('Controller must be an instance of PSX\Framework\ApplicationStackInterface');
        }
    }

    protected function resolveFilters($filters)
    {
        if (empty($filters) || !is_array($filters)) {
            return [];
        }

        $result = [];
        foreach ($filters as $filter) {
            if (is_string($filter)) {
                $result[] = $this->objectBuilder->getObject($filter, [], 'PSX\Framework\Filter\FilterInterface');
            } elseif ($filter instanceof FilterInterface) {
                $result[] = $filter;
            } elseif ($filter instanceof Closure) {
                $result[] = $filter;
            } else {
                throw new RuntimeException('Filter must be either a classname, instance of PSX\Framework\Filter\FilterInterface or closure');
            }
        }

        return $result;
    }
}

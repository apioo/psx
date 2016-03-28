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

namespace PSX\Framework\Dependency;

use Doctrine\Common\Annotations;
use Doctrine\Common\Cache as DoctrineCache;
use PSX\Api\Listing;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Dispatch\ApplicationStackFactory;
use PSX\Framework\Dispatch\ControllerFactory;
use PSX\Framework\Dispatch\RequestFactory;
use PSX\Framework\Dispatch\ResponseFactory;
use PSX\Framework\Dispatch\Sender\Basic as BasicSender;
use PSX\Framework\Loader;
use PSX\Framework\Session\Session;
use PSX\Framework\Template;
use PSX\Framework\Exception;

/**
 * Controller
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait Framework
{
    /**
     * @return \PSX\Framework\Config\Config
     */
    public function getConfig()
    {
        $config = new Config($this->appendDefaultConfig());
        $config = $config->merge(Config::fromFile($this->getParameter('config.file')));

        return $config;
    }

    /**
     * @return \PSX\Framework\Template\TemplateInterface
     */
    public function getTemplate()
    {
        return new Template\Engine\Php();
    }

    /**
     * @return \PSX\Framework\Dependency\ObjectBuilderInterface
     */
    public function getObjectBuilder()
    {
        return new ObjectBuilder(
            $this,
            $this->get('annotation_reader')
        );
    }

    /**
     * @return \PSX\Framework\Exception\ConverterInterface
     */
    public function getExceptionConverter()
    {
        return new Exception\Converter($this->get('config')->get('psx_debug'));
    }

    /**
     * @return \PSX\Framework\Session\Session
     */
    public function getSession()
    {
        $name    = $this->hasParameter('session.name') ? $this->getParameter('session.name') : 'psx';
        $session = new Session($name);

        if (PHP_SAPI != 'cli') {
            $session->start();
        }

        return $session;
    }

    /**
     * @return \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    public function getApplicationStackFactory()
    {
        return new ApplicationStackFactory($this->get('object_builder'));
    }

    /**
     * @return \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    public function getControllerFactory()
    {
        return new ControllerFactory($this->get('object_builder'));
    }

    /**
     * @return \PSX\Framework\Dispatch\SenderInterface
     */
    public function getDispatchSender()
    {
        return new BasicSender();
    }

    /**
     * @return \PSX\Framework\Loader\LocationFinderInterface
     */
    public function getLoaderLocationFinder()
    {
        return new Loader\LocationFinder\RoutingParser($this->get('routing_parser'));
    }

    /**
     * @return \PSX\Framework\Loader\CallbackResolverInterface
     */
    public function getLoaderCallbackResolver()
    {
        return new Loader\CallbackResolver\DependencyInjector($this->get('application_stack_factory'));
    }

    /**
     * @return \PSX\Framework\Loader\Loader
     */
    public function getLoader()
    {
        return new Loader\Loader(
            $this->get('loader_location_finder'),
            $this->get('loader_callback_resolver'),
            $this->get('event_dispatcher'),
            $this->get('logger'),
            $this->get('object_builder'),
            $this->get('config')
        );
    }

    /**
     * @return \PSX\Framework\Dispatch\RequestFactoryInterface
     */
    public function getRequestFactory()
    {
        return new RequestFactory($this->get('config'));
    }

    /**
     * @return \PSX\Framework\Dispatch\ResponseFactoryInterface
     */
    public function getResponseFactory()
    {
        return new ResponseFactory();
    }

    /**
     * @return \PSX\Framework\Dispatch\Dispatch
     */
    public function getDispatch()
    {
        return new Dispatch(
            $this->get('config'),
            $this->get('loader'),
            $this->get('application_stack_factory'),
            $this->get('dispatch_sender'),
            $this->get('event_dispatcher'),
            $this->get('exception_converter')
        );
    }

    /**
     * @return \PSX\Framework\Loader\RoutingParserInterface
     */
    public function getRoutingParser()
    {
        $routingParser = new Loader\RoutingParser\RoutingFile($this->get('config')->get('psx_routing'));

        if ($this->get('config')->get('psx_debug')) {
            return $routingParser;
        } else {
            return new Loader\RoutingParser\CachedParser($routingParser, $this->get('cache'));
        }
    }

    /**
     * @return \PSX\Framework\Loader\ReverseRouter
     */
    public function getReverseRouter()
    {
        return new Loader\ReverseRouter(
            $this->get('routing_parser'),
            $this->get('config')->get('psx_url'),
            $this->get('config')->get('psx_dispatch')
        );
    }

    /**
     * @return \PSX\Api\ListingInterface
     */
    public function getResourceListing()
    {
        $resourceListing = new Listing\ControllerDocumentation($this->get('routing_parser'), $this->get('controller_factory'));

        if ($this->get('config')->get('psx_debug')) {
            return $resourceListing;
        } else {
            return new Listing\CachedListing($resourceListing, $this->get('cache'));
        }
    }
}

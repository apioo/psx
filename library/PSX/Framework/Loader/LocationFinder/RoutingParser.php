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

namespace PSX\Framework\Loader\LocationFinder;

use PSX\Framework\Loader\Context;
use PSX\Framework\Loader\LocationFinderInterface;
use PSX\Framework\Loader\PathMatcher;
use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParserInterface;
use PSX\Http\RequestInterface;
use PSX\Uri\Uri;

/**
 * Location finder which gets a collection of routes from an routing parser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingParser implements LocationFinderInterface
{
    protected $routingParser;

    public function __construct(RoutingParserInterface $routingParser)
    {
        $this->routingParser = $routingParser;
    }

    public function resolve(RequestInterface $request, Context $context)
    {
        $routingCollection = $this->routingParser->getCollection();
        $method            = $request->getMethod();
        $pathMatcher       = new PathMatcher($request->getUri()->getPath());

        foreach ($routingCollection as $routing) {
            $parameters = array();

            $methodMatch = $routing[RoutingCollection::ROUTING_METHODS] === ['ANY'] ||
                in_array($method, $routing[RoutingCollection::ROUTING_METHODS]);

            if ($methodMatch &&
                $pathMatcher->match($routing[RoutingCollection::ROUTING_PATH], $parameters)) {
                $source = $routing[RoutingCollection::ROUTING_SOURCE];

                if ($source[0] == '~') {
                    $request->setUri(new Uri(substr($source, 1)));

                    return $this->resolve($request, $context);
                }

                $context->set(Context::KEY_PATH, $routing[RoutingCollection::ROUTING_PATH]);
                $context->set(Context::KEY_FRAGMENT, $parameters);
                $context->set(Context::KEY_SOURCE, $source);

                return $request;
            }
        }

        return null;
    }
}

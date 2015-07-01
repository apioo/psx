<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Loader\LocationFinder;

use PSX\Http\RequestInterface;
use PSX\Loader\Context;
use PSX\Loader\LocationFinderInterface;
use PSX\Loader\PathMatcher;
use PSX\Loader\RoutingCollection;
use PSX\Loader\RoutingParserInterface;
use PSX\Uri;

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

            if (in_array($method, $routing[RoutingCollection::ROUTING_METHODS]) &&
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

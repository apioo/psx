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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		foreach($routingCollection as $routing)
		{
			$parameters = array();

			if(in_array($method, $routing[RoutingCollection::ROUTING_METHODS]) && 
				$pathMatcher->match($routing[RoutingCollection::ROUTING_PATH], $parameters))
			{
				$source = $routing[RoutingCollection::ROUTING_SOURCE];

				if($source[0] == '~')
				{
					$request->setUri(new Uri(substr($source, 1)));

					return $this->resolve($request, $context);
				}

				$context->set(Context::KEY_FRAGMENT, $parameters);
				$context->set(Context::KEY_SOURCE, $source);

				return $request;
			}
		}

		return null;
	}
}

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

use PSX\Loader\InvalidPathException;
use PSX\Loader\Location;
use PSX\Loader\LocationFinderInterface;
use PSX\Loader\PathMatcher;
use PSX\Loader\RoutingCollection;
use PSX\Loader\RoutingParserInterface;
use ReflectionClass;

/**
 * Location finder which gets a collection of routes from an routing parser. If
 * an cache handler is given the collection gets cached with the handler
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

	public function resolve($method, $pathInfo)
	{
		$routingCollection = $this->routingParser->getCollection();
		$pathMatcher       = new PathMatcher($pathInfo);

		foreach($routingCollection as $routing)
		{
			$parameters = array();

			if(in_array($method, $routing[RoutingCollection::ROUTING_METHODS]) && 
				$pathMatcher->match($routing[RoutingCollection::ROUTING_PATH], $parameters))
			{
				$source = $routing[RoutingCollection::ROUTING_SOURCE];

				if($source[0] == '~')
				{
					return $this->resolve($method, substr($source, 1));
				}

				$location = new Location();
				$location->setParameter(Location::KEY_FRAGMENT, $parameters);
				$location->setParameter(Location::KEY_SOURCE, $source);

				return $location;
			}
		}
	}
}

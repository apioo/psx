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

namespace PSX\Loader\RoutingParser;

use PSX\Cache;
use PSX\Loader\RoutingCollection;
use PSX\Loader\RoutingParserInterface;

/**
 * CachedParser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CachedParser implements RoutingParserInterface
{
	const CACHE_KEY = 'routing_file';

	protected $routingParser;
	protected $cacheHandler;
	protected $expire;

	public function __construct(RoutingParserInterface $routingParser, Cache\HandlerInterface $cacheHandler, $expire = 0)
	{
		$this->routingParser = $routingParser;
		$this->cacheHandler  = $cacheHandler;
	}

	public function getCollection()
	{
		if(($cacheItem = $this->cacheHandler->load(self::CACHE_KEY)) !== false)
		{
			$collection = json_decode($cacheItem->getContent());

			return new RoutingCollection($collection);
		}
		else
		{
			$collection = $this->routingParser->getCollection();

			$this->cacheHandler->write(self::CACHE_KEY, json_encode($collection->getAll()), $this->expire);

			return $collection;
		}
	}
}

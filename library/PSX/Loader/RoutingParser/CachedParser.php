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

namespace PSX\Loader\RoutingParser;

use Psr\Cache\CacheItemPoolInterface;
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
	protected $cache;
	protected $expire;

	public function __construct(RoutingParserInterface $routingParser, CacheItemPoolInterface $cache, $expire = null)
	{
		$this->routingParser = $routingParser;
		$this->cache         = $cache;
		$this->expire        = $expire;
	}

	public function getCollection()
	{
		$item = $this->cache->getItem(self::CACHE_KEY);

		if($item->isHit())
		{
			return $item->get();
		}
		else
		{
			$collection = $this->routingParser->getCollection();

			$item->set($collection, $this->expire);

			$this->cache->save($item);

			return $collection;
		}
	}
}

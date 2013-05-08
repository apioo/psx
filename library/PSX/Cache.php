<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use DateInterval;
use PSX\Cache\Handler\File;
use PSX\Cache\Item;
use PSX\Cache\HandlerInterface;

/**
 * Provides a general caching mechanism. This class abstracts how cached items
 * are saved (i.e. file, sql, ...) and handels expire times. Here an example how
 * you can use the cache class. As [key] you must provide a unique key.
 * <code>
 * $cache = new Cache('[key]');
 *
 * if(($content = $cache->load()) === false)
 * {
 * 	// here some complex stuff so that it is worth to cache the content
 * 	$content = 'test';
 *
 * 	$cache->write($content);
 * }
 *
 * echo $content;
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Cache
{
	/**
	 * The cache key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The expire time of the cache in seconds
	 *
	 * @var integer
	 */
	public $expire;

	/**
	 * Whether to write the cache or not
	 *
	 * @var boolean
	 */
	public $enabled;

	/**
	 * The handler for the class
	 *
	 * @var PSX\Cache\HandlerInterface
	 */
	private $handler;

	/**
	 * To create an cache object we need an $key wich identifies the cache. The
	 * handler is an object of type PSX\Cache\HandlerInterface wich is used to
	 * load and write the cache. Optional you can set the $expire time how long
	 * the cache remains if not set the default config cache expire time is
	 * used.
	 *
	 * @param string $key
	 * @param integer|DateInterval $expire
	 * @param PSX\Cache\HandlerInterface $handler
	 */
	public function __construct($key, $expire = 0, HandlerInterface $handler = null)
	{
		if(!is_numeric($expire))
		{
			$interval = $expire instanceof DateInterval ? $expire : new DateInterval($expire);
			$now      = new DateTime();
			$tstamp   = $now->getTimestamp();

			$now->add($interval);

			$this->expire = $now->getTimestamp() - $tstamp;
		}
		else
		{
			$this->expire = $expire;
		}

		$this->key     = md5($key);
		$this->handler = $handler !== null ? $handler : new File();
		$this->enabled = true;
	}

	public function setEnabled($enabled)
	{
		$this->enabled = (boolean) $enabled;
	}

	/**
	 * If caching is enabled and the item exists and is not expired we load the
	 * cache from the handler if not we return false
	 *
	 * @return false|string
	 */
	public function load()
	{
		if($this->enabled && ($item = $this->handler->load($this->key)))
		{
			if($item instanceof Item)
			{
				if($this->expire > 0 && $item->getTime() !== null && (time() - $item->getTime()) > $this->expire)
				{
					return false;
				}
				else
				{
					return $item->getContent();
				}
			}
		}

		return false;
	}

	/**
	 * Write the string $content to the cache by using the handler.
	 *
	 * @param string $content
	 * @return void
	 */
	public function write($content)
	{
		if($this->enabled)
		{
			$this->handler->write($this->key, $content, $this->expire);
		}
	}

	/**
	 * Remove the key from the cache using the handler
	 *
	 * @return void
	 */
	public function remove()
	{
		if($this->enabled)
		{
			$this->handler->remove($this->key);
		}
	}
}


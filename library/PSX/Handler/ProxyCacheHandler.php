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

namespace PSX\Handler;

use BadMethodCallException;
use InvalidArgumentException;
use RuntimeException;
use PSX\Data\RecordInterface;

/**
 * Handler wich can be used to cache handler results. This can be useful if your
 * handler is expensive because of an complex sql query or api call. The result
 * can be cached through different handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ProxyCacheHandler extends HandlerAbstract
{
	protected $handler;

	protected $_cache = array();

	public function __construct(HandlerQueryInterface $handler)
	{
		$this->handler = $handler;
	}

	public function __call($name, array $args)
	{
		$isCacheable = false;
		switch($name)
		{
			case 'getAll':
			case 'getBy':
			case 'getOneBy':
			//case 'get':
			case 'getResultSet':
			case 'getSupportedFields':
			case 'getCount':
				$isCacheable = true;
				break;
		}

		if($isCacheable)
		{
			$key    = md5(json_encode($args));
			$return = $this->getFromCache($key);

			if($return !== null)
			{
				return $return;
			}
		}

		$return = call_user_func_array(array($this->handler, $name), $args);

		if($isCacheable)
		{
			$this->setToCache($key, $return);
		}

		return $return;
	}

	protected function getFromCache($key)
	{
		if(isset($this->_cache[$key]))
		{
			return $this->_cache[$key];
		}

		return null;
	}

	protected function setToCache($key, $return)
	{
		$this->_cache[$key] = $return;
	}
}

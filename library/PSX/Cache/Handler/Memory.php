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

namespace PSX\Cache\Handler;

use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;

/**
 * Memory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Memory implements HandlerInterface
{
	protected $_container = array();

	public function load($key)
	{
		if(isset($this->_container[$key]))
		{
			$item = $this->_container[$key];

			if(!$item->hasExpiration() || $item->getExpiration()->getTimestamp() >= time())
			{
				return new Item($item->getKey(), $item->get(), true, $item->getExpiration());
			}
		}

		return new Item($key, null, false);
	}

	public function write(Item $item)
	{
		$this->_container[$item->getKey()] = $item;
	}

	public function remove($key)
	{
		if(isset($this->_container[$key]))
		{
			unset($this->_container[$key]);
		}
	}

	public function removeAll()
	{
		$this->_container = array();

		return true;
	}
}


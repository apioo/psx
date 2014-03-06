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

namespace PSX\Handler;

use InvalidArgumentException;
use PSX\Data\RecordInterface;
use PSX\Cache\HandlerInterface as CacheHandlerInterface;
use PSX\Sql\Condition;

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
	protected $cache;
	protected $expire;

	public function __construct(HandlerQueryInterface $handler, CacheHandlerInterface $cache, $expire = 0)
	{
		$this->handler = $handler;
		$this->cache   = $cache;
		$this->expire  = $expire;
	}

	public function setExpire($expire)
	{
		$this->expire = $expire;
	}

	public function getAll(array $fields = array(), $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$key    = '__PC__' . md5(json_encode(array(__METHOD__, $fields, $startIndex, $count, $sortBy, $sortOrder, (string) $con)));
		$return = $this->cache->load($key);

		if($return !== false)
		{
			return unserialize($return->getContent());
		}

		$return = $this->handler->getAll($fields, $startIndex, $count, $sortBy, $sortOrder, $con);

		$this->cache->write($key, serialize($return), $this->expire);

		return $return;
	}

	public function get($id, array $fields = array())
	{
		return $this->handler->get($id, $fields);
	}

	public function getSupportedFields()
	{
		$key    = '__PC__' . md5(json_encode(array(__METHOD__)));
		$return = $this->cache->load($key);

		if($return !== false)
		{
			return unserialize($return->getContent());
		}

		$return = $this->handler->getSupportedFields();

		$this->cache->write($key, serialize($return), $this->expire);

		return $return;
	}

	public function getCount(Condition $con = null)
	{
		$key    = '__PC__' . md5(json_encode(array(__METHOD__, (string) $con)));
		$return = $this->cache->load($key);

		if($return !== false)
		{
			return unserialize($return->getContent());
		}

		$return = $this->handler->getCount($con);

		$this->cache->write($key, serialize($return), $this->expire);

		return $return;
	}

	public function getRecord($id = null)
	{
		return $this->handler->getRecord($id);
	}

	public function create(RecordInterface $record)
	{
		if($this->handler instanceof HandlerManipulationInterface)
		{
			$this->handler->create($record);
		}
		else
		{
			throw new InvalidArgumentException('Handler is not an manipulation instance');
		}
	}

	public function update(RecordInterface $record)
	{
		if($this->handler instanceof HandlerManipulationInterface)
		{
			$this->handler->update($record);
		}
		else
		{
			throw new InvalidArgumentException('Handler is not an manipulation instance');
		}
	}

	public function delete(RecordInterface $record)
	{
		if($this->handler instanceof HandlerManipulationInterface)
		{
			$this->handler->delete($record);
		}
		else
		{
			throw new InvalidArgumentException('Handler is not an manipulation instance');
		}
	}

	public function getRestrictedFields()
	{
		return $this->handler->getRestrictedFields();
	}

	public function setRestrictedFields(array $restrictedFields)
	{
		$this->handler->setRestrictedFields($restrictedFields);
	}
}

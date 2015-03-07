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

namespace PSX\Sql;

use Doctrine\DBAL\Connection;
use PSX\Cache\CacheItemPoolInterface;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;

/**
 * TableManager
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TableManager implements TableManagerInterface
{
	/**
	 * @var Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @var PSX\Sql\Table\ReaderInterface
	 */
	protected $defaultReader;

	/**
	 * @var PSX\Cache\CacheItemPoolInterface
	 */
	protected $cache;

	/**
	 * @var integer
	 */
	protected $expire;

	protected $_container;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Sets the default reader wich is used if no explicit reader was specified
	 *
	 * @param PSX\Sql\Table\ReaderInterface $defaultReader
	 */
	public function setDefaultReader(ReaderInterface $defaultReader)
	{
		$this->defaultReader = $defaultReader;
	}

	/**
	 * If set the table definition results are cached with the specific handler
	 * Note the cache doesnt expire so you have to delete the cache manually
	 * if the definition has changed
	 *
	 * @param PSX\Cache\CacheItemPoolInterface $cache
	 * @param integer $expire
	 */
	public function setCache(CacheItemPoolInterface $cache, $expire = null)
	{
		$this->cache  = $cache;
		$this->expire = $expire;
	}

	public function getTable($tableName)
	{
		if(isset($this->_container[$tableName]))
		{
			return $this->_container[$tableName];
		}

		// if a cache handler is set try to read the definition from the cache
		// but only if we haven an reader
		if($this->cache !== null && $this->defaultReader !== null)
		{
			$key  = '__TD__' . md5(__METHOD__ . '-' . $tableName);
			$item = $this->cache->getItem($key);

			if($item->isHit())
			{
				$definition = $item->get();

				$this->_container[$tableName] = new Table($this->connection,
					$definition->getName(),
					$definition->getColumns(), 
					$definition->getConnections());
			}
		}

		if(!isset($this->_container[$tableName]))
		{
			if($this->defaultReader === null)
			{
				// we assume that $tableName is an class name of an 
				// TableInterface implementation
				$this->_container[$tableName] = new $tableName($this->connection);
			}
			else
			{
				$definition = $this->defaultReader->getTableDefinition($tableName);

				$this->_container[$tableName] = new Table($this->connection,
					$definition->getName(),
					$definition->getColumns(), 
					$definition->getConnections());

				// if a cache item is set write definition to the cache
				if(isset($item))
				{
					$item->set($definition, $this->expire);

					$this->cache->save($item);
				}
			}
		}

		return $this->_container[$tableName];
	}
}

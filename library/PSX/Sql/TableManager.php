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

namespace PSX\Sql;

use PSX\Cache;
use PSX\Sql;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;

/**
 * TableManager
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableManager implements TableManagerInterface
{
	/**
	 * @var PSX\Sql
	 */
	protected $sql;

	/**
	 * @var PSX\Sql\Table\ReaderInterface
	 */
	protected $defaultReader;

	/**
	 * @var PSX\Cache\HandlerInterface
	 */
	protected $cacheHandler;

	protected $_container;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;
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
	 * @param PSX\Cache\HandlerInterface $handler
	 */
	public function setCacheHandler(Cache\HandlerInterface $cacheHandler)
	{
		$this->cacheHandler = $cacheHandler;
	}

	/**
	 * Returns an table instance from the given table name
	 *
	 * @param string $tableName
	 * @return PSX\Sql\TableInterface
	 */
	public function getTable($tableName)
	{
		if(isset($this->_container[$tableName]))
		{
			return $this->_container[$tableName];
		}

		// if a cache handler is set try to read the definition from the cache
		// but only if we haven an reader
		if($this->cacheHandler !== null && $this->defaultReader !== null)
		{
			$key  = '__TD__' . md5(__METHOD__ . '-' . $tableName);
			$item = $this->cacheHandler->load($key);

			if($item instanceof Cache\Item)
			{
				$definition = unserialize($item->getContent());

				$this->_container[$tableName] = new Table($this->sql,
					$definition->getName(),
					$definition->getColumns(), 
					$definition->getConnections());
			}
		}

		if(!isset($this->_container[$tableName]))
		{
			$definition = null;

			if($this->defaultReader === null)
			{
				// we assume that $tableName is an class name of an 
				// TableInterface implementation
				$this->_container[$tableName] = new $tableName($this->sql);
			}
			else
			{
				$definition = $this->defaultReader->getTableDefinition($tableName);
			}

			if($definition instanceof Definition)
			{
				$this->_container[$tableName] = new Table($this->sql,
					$definition->getName(),
					$definition->getColumns(), 
					$definition->getConnections());

				// if a cache handler is set write definition to the cache
				if($this->cacheHandler !== null)
				{
					$this->cacheHandler->write($key, serialize($definition), 0);
				}
			}
		}

		return $this->_container[$tableName];
	}
}

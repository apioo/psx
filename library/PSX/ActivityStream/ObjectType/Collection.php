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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;
use PSX\Data\CollectionInterface;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInterface;

/**
 * Collection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Collection extends Object implements CollectionInterface
{
	protected $totalItems;
	protected $items;
	protected $itemsAfter;
	protected $itemsBefore;
	protected $itemsPerPage;
	protected $startIndex;
	protected $first;
	protected $last;
	protected $prev;
	protected $next;
	protected $current;
	protected $self;

	private $_pointer;

	public function __construct(array $items = array())
	{
		$this->items = $items;
	}

	public function setTotalItems($totalItems)
	{
		$this->totalItems = $totalItems;
	}
	
	public function getTotalItems()
	{
		return $this->totalItems;
	}

	/**
	 * @param array<PSX\ActivityStream\ObjectType\Activity>
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function setItemsAfter($itemsAfter)
	{
		$this->itemsAfter = $itemsAfter;
	}
	
	public function getItemsAfter()
	{
		return $this->itemsAfter;
	}

	public function setItemsBefore($itemsBefore)
	{
		$this->itemsBefore = $itemsBefore;
	}
	
	public function getItemsBefore()
	{
		return $this->itemsBefore;
	}

	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}
	
	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	public function setStartIndex($startIndex)
	{
		$this->startIndex = $startIndex;
	}
	
	public function getStartIndex()
	{
		return $this->startIndex;
	}

	public function setFirst($first)
	{
		$this->first = $first;
	}
	
	public function getFirst()
	{
		return $this->first;
	}

	public function setLast($last)
	{
		$this->last = $last;
	}
	
	public function getLast()
	{
		return $this->last;
	}

	public function setPrev($prev)
	{
		$this->prev = $prev;
	}
	
	public function getPrev()
	{
		return $this->prev;
	}

	public function setNext($next)
	{
		$this->next = $next;
	}
	
	public function getNext()
	{
		return $this->next;
	}

	public function setCurrent($current)
	{
		$this->current = $current;
	}
	
	public function getCurrent()
	{
		return $this->current;
	}

	public function setSelf($self)
	{
		$this->self = $self;
	}
	
	public function getSelf()
	{
		return $this->self;
	}

	public function add(RecordInterface $object)
	{
		$this->items[] = $object;
	}

	public function clear()
	{
		$this->items = array();
		$this->rewind();
	}

	public function isEmpty()
	{
		return $this->count() == 0;
	}

	public function get($key)
	{
		return $this->items[$key];
	}

	public function set($key, RecordInterface $object)
	{
		$this->items[$key] = $object;
	}

	public function toArray()
	{
		return $this->items;
	}

	// Iterator
	public function current()
	{
		return current($this->items);
	}

	public function key()
	{
		return key($this->items);
	}

	public function next()
	{
		return $this->_pointer = next($this->items);
	}

	public function rewind()
	{
		$this->_pointer = reset($this->items);
	}

	public function valid()
	{
		return $this->_pointer;
	}

	// Countable
	public function count()
	{
		return count($this->items);
	}
}

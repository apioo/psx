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

namespace PSX\ActivityStream\ObjectType;

use DateTime;
use PSX\ActivityStream\Object;
use PSX\Data\CollectionInterface;
use PSX\Data\RecordInterface;

/**
 * Collection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
	 * @param \PSX\ActivityStream\ObjectType\Activity[] $items
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
	}

	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param \PSX\DateTime $itemsAfter
	 */
	public function setItemsAfter(DateTime $itemsAfter)
	{
		$this->itemsAfter = $itemsAfter;
	}
	
	public function getItemsAfter()
	{
		return $this->itemsAfter;
	}

	/**
	 * @param \PSX\DateTime $itemsBefore
	 */
	public function setItemsBefore(DateTime $itemsBefore)
	{
		$this->itemsBefore = $itemsBefore;
	}
	
	public function getItemsBefore()
	{
		return $this->itemsBefore;
	}

	/**
	 * @param integer $itemsPerPage
	 */
	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}
	
	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	/**
	 * @param integer $startIndex
	 */
	public function setStartIndex($startIndex)
	{
		$this->startIndex = $startIndex;
	}
	
	public function getStartIndex()
	{
		return $this->startIndex;
	}

	/**
	 * @param \PSX\ActivityStream\ObjectFactory $first
	 */
	public function setFirst($first)
	{
		$this->first = $first;
	}
	
	public function getFirst()
	{
		return $this->first;
	}

	/**
	 * @param \PSX\ActivityStream\ObjectFactory $last
	 */
	public function setLast($last)
	{
		$this->last = $last;
	}
	
	public function getLast()
	{
		return $this->last;
	}

	/**
	 * @param \PSX\ActivityStream\ObjectFactory $prev
	 */
	public function setPrev($prev)
	{
		$this->prev = $prev;
	}
	
	public function getPrev()
	{
		return $this->prev;
	}

	/**
	 * @param \PSX\ActivityStream\ObjectFactory $next
	 */
	public function setNext($next)
	{
		$this->next = $next;
	}
	
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * @param \PSX\ActivityStream\ObjectFactory $current
	 */
	public function setCurrent($current)
	{
		$this->current = $current;
	}
	
	public function getCurrent()
	{
		return $this->current;
	}

	/**
	 * @param \PSX\ActivityStream\ObjectFactory $self
	 */
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

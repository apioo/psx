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

namespace PSX\Util;

use Countable;
use IteratorAggregate;
use SplPriorityQueue;

/**
 * A priority queue which you can iterate multiple times. The SplPriorityQueue
 * removes an element after traversing
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PriorityQueue implements IteratorAggregate, Countable
{
	protected $queue;

	public function __construct()
	{
		$this->queue = new SplPriorityQueue();
	}

	public function insert($value, $priority)
	{
		$this->queue->insert($value, $priority);
	}

	public function count()
	{
		return $this->queue->count();
	}

	public function getIterator()
	{
		return clone $this->queue;
	}
}

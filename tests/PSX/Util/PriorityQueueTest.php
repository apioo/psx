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

/**
 * PriorityQueueTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PriorityQueueTest extends \PHPUnit_Framework_TestCase
{
	public function testQueue()
	{
		$queue = new PriorityQueue();
		$queue->insert('foo', 0);
		$queue->insert('test', 16);
		$queue->insert('bar', 8);

		$this->assertInstanceOf('Countable', $queue);
		$this->assertInstanceOf('IteratorAggregate', $queue);

		$this->assertEquals(3, $queue->count());
		$this->assertEquals(3, count($queue));

		$iterator = $queue->getIterator();

		$this->assertEquals('test', $iterator->current());

		$iterator->next();

		$this->assertEquals('bar', $iterator->current());

		$iterator->next();

		$this->assertEquals('foo', $iterator->current());

		$iterator->next();

		$this->assertEmpty($iterator->current());
	}
}

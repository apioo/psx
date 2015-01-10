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

namespace PSX\Cache\Handler;

use PDOException;
use PSX\CacheTest;
use PSX\Sql\Table\ColumnAllocation;

/**
 * NullTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class NullTest extends \PHPUnit_Framework_TestCase
{
	public function testLoad()
	{
		$handler = new Null();
		$item    = $handler->load('key');

		$this->assertInstanceOf('PSX\Cache\Item', $item);
		$this->assertEquals('key', $item->getKey());
		$this->assertEquals(null, $item->get());
		$this->assertEquals(false, $item->isHit());
	}

	public function testWrite()
	{
		$handler = new Null();
		$item    = $handler->load('key');

		$item->set('foobar');

		$handler->write($item);
	}

	public function testRemove()
	{
		$handler = new Null();
		$handler->remove('foobar');
	}

	public function testRemoveAll()
	{
		$handler = new Null();
		$handler->removeAll();
	}
}

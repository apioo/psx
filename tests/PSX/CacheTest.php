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

namespace PSX;

use DateInterval;
use PSX\Cache\Handler;

/**
 * CacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
	protected function getHandler()
	{
		return new Handler\File();
	}

	public function testCache()
	{
		$cache = new Cache($this->getHandler());

		// remove any existing cache
		$cache->clear();

		// get an item which does not exist
		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(false, $item->exists());
		$this->assertEquals(null, $item->get());

		// create an item which does not expire
		$item->set('foobar');

		$cache->save($item);

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(true, $item->exists());
		$this->assertEquals('foobar', $item->get());

		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(true, $item->isHit());
		$this->assertEquals(true, $item->exists());
		$this->assertEquals('foobar', $item->get());

		// check whether multiple load calls return the same result
		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(true, $item->isHit());
		$this->assertEquals(true, $item->exists());
		$this->assertEquals('foobar', $item->get());

		// remove the item
		$cache->deleteItems(['key']);

		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(false, $item->exists());
		$this->assertEquals(null, $item->get());
	}

	public function testCacheExpire()
	{
		$cache = new Cache($this->getHandler());

		// remove any existing cache
		$cache->clear();

		// get an item which does not exist
		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(false, $item->exists());
		$this->assertEquals(null, $item->get());

		// create an item which expires in 2 seconds
		$item->set('foobar', 2);

		$cache->save($item);

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(true, $item->exists());
		$this->assertEquals('foobar', $item->get());

		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(true, $item->isHit());
		$this->assertEquals(true, $item->exists());
		$this->assertEquals('foobar', $item->get());

		// we wait 4 seconds so that the item gets expired
		sleep(4);

		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(false, $item->exists());
		$this->assertEquals(null, $item->get());

		// remove the item
		$cache->deleteItems(['key']);

		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(false, $item->exists());
		$this->assertEquals(null, $item->get());
	}
}


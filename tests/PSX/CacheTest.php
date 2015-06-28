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

namespace PSX;

use PSX\Cache\Handler;

/**
 * CacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

	public function testClear()
	{
		$cache = new Cache($this->getHandler());

		$item = $cache->getItem('key');
		$item->set('foobar');

		$cache->save($item);

		$items = $cache->getItems(['key']);

		$this->assertArrayHasKey(0, $items);

		$item = $items[0];

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(true, $item->isHit());
		$this->assertEquals(true, $item->exists());
		$this->assertEquals('foobar', $item->get());

		$cache->clear();

		$item = $cache->getItem('key');

		$this->assertEquals('key', $item->getKey());
		$this->assertInstanceOf('DateTime', $item->getExpiration());
		$this->assertEquals(false, $item->isHit());
		$this->assertEquals(false, $item->exists());
		$this->assertEquals(null, $item->get());
	}
}


<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Cache\Tests;

use Doctrine\Common\Cache\ArrayCache;
use PSX\Cache\Pool;

/**
 * CacheTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PoolTest extends \PHPUnit_Framework_TestCase
{
    public function testCache()
    {
        $cache = $this->newCachePool();

        // remove any existing cache
        $cache->clear();

        // get an item which does not exist
        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals(null, $item->get());
        $this->assertEquals(0, $item->getTtl());

        // create an item which does not expire
        $item->set('foobar');

        $cache->save($item);

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals('foobar', $item->get());
        $this->assertEquals(0, $item->getTtl());

        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(true, $item->isHit());
        $this->assertEquals('foobar', $item->get());
        $this->assertEquals(0, $item->getTtl());

        // check whether multiple load calls return the same result
        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(true, $item->isHit());
        $this->assertEquals('foobar', $item->get());
        $this->assertEquals(0, $item->getTtl());

        // remove the item
        $cache->deleteItems(['key']);

        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals(null, $item->get());
        $this->assertEquals(0, $item->getTtl());
    }

    public function testCacheExpire()
    {
        $cache = $this->newCachePool();
        $expriesAfter = 1;

        // remove any existing cache
        $cache->clear();

        // get an item which does not exist
        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals(null, $item->get());
        $this->assertEquals(0, $item->getTtl());

        // create an item which expires in 1 second
        $item->set('foobar');
        $item->expiresAfter($expriesAfter);

        $cache->save($item);

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals('foobar', $item->get());
        $this->assertEquals($expriesAfter, $item->getTtl());

        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(true, $item->isHit());
        $this->assertEquals('foobar', $item->get());
        $this->assertEquals(0, $item->getTtl());

        // we wait 2 seconds so that the item gets expired
        sleep(2);

        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals(null, $item->get());
        $this->assertEquals(0, $item->getTtl());

        // remove the item
        $cache->deleteItems(['key']);

        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals(null, $item->get());
        $this->assertEquals(0, $item->getTtl());
    }

    public function testClear()
    {
        $cache = $this->newCachePool();

        $item = $cache->getItem('key');
        $item->set('foobar');

        $cache->save($item);

        $items = $cache->getItems(['key']);

        $this->assertArrayHasKey(0, $items);

        $item = $items[0];

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(true, $item->isHit());
        $this->assertEquals('foobar', $item->get());
        $this->assertEquals(0, $item->getTtl());

        $cache->clear();

        $item = $cache->getItem('key');

        $this->assertEquals('key', $item->getKey());
        $this->assertEquals(false, $item->isHit());
        $this->assertEquals(null, $item->get());
        $this->assertEquals(0, $item->getTtl());
    }

    protected function newCachePool()
    {
        return new Pool(new ArrayCache());
    }
}

<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	protected function getHandler()
	{
		return new Handler\File();
	}

	public function testMinimalCache()
	{
		$cache = new Cache('[key]');

		$this->checkUnlimitedCache($cache);
	}

	public function testCache()
	{
		$cache = new Cache('[key]', 0, $this->getHandler());

		$this->checkUnlimitedCache($cache);
	}

	public function testCacheExpires()
	{
		$cache = new Cache('[key]', 2, $this->getHandler()); // expires in 2 seconds

		$this->checkLimitedCache($cache);
	}

	public function testCacheExpiresString()
	{
		$cache = new Cache('[key]', 'PT2S', $this->getHandler()); // expires in 2 seconds

		$this->checkLimitedCache($cache);
	}

	public function testCacheExpiresDateInterval()
	{
		$cache = new Cache('[key]', new DateInterval('PT2S'), $this->getHandler()); // expires in 2 seconds

		$this->checkLimitedCache($cache);
	}

	public function testCacheDisabled()
	{
		$cache = new Cache('[key]');

		$this->checkDisabledCache($cache);
	}

	protected function checkUnlimitedCache(Cache $cache)
	{
		$cache->remove();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(true, $cache->exists());
		$this->assertEquals(false, $cache->isExpired());
		$this->assertInstanceOf('\PSX\Cache\Item', $cache->get());
		$this->assertEquals('foobar', $cache->get()->getContent());
		$this->assertEquals('foobar', $content);

		// check whether multiple load calls return the same result
		$this->assertEquals('foobar', $cache->load());

		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);
	}

	protected function checkLimitedCache(Cache $cache)
	{
		$cache->remove();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(2, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(2, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(2, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(true, $cache->exists());
		$this->assertEquals(false, $cache->isExpired());
		$this->assertInstanceOf('\PSX\Cache\Item', $cache->get());
		$this->assertEquals('foobar', $cache->get()->getContent());
		$this->assertEquals('foobar', $content);

		// check whether multiple load calls return the same result
		$this->assertEquals('foobar', $cache->load());

		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(2, $cache->getExpire());
		$this->assertEquals(true, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);
	}

	protected function checkDisabledCache(Cache $cache)
	{
		// remove
		$cache->remove();
		$cache->setEnabled(false);

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(false, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(false, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(false, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);

		// check whether multiple load calls return the same result
		$this->assertEquals(false, $cache->load());

		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(md5('[key]'), $cache->getKey());
		$this->assertEquals(0, $cache->getExpire());
		$this->assertEquals(false, $cache->isEnabled());
		$this->assertEquals(false, $cache->exists());
		$this->assertEquals(true, $cache->isExpired());
		$this->assertEquals(null, $cache->get());
		$this->assertEquals(false, $content);
	}
}


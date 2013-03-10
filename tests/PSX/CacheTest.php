<?php
/*
 *  $Id: CacheTest.php 636 2012-09-01 10:32:42Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_CacheTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 636 $
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
		$cache = new Cache(__METHOD__);
		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals('foobar', $content);

		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);
	}

	public function testCache()
	{
		$cache = new Cache(__METHOD__, 0, $this->getHandler());
		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals('foobar', $content);

		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);
	}

	public function testCacheExpires()
	{
		$cache = new Cache(__METHOD__, 2, $this->getHandler()); // expires in 2 seconds
		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals('foobar', $content);

		sleep(3); // wait 2 seconds

		$content = $cache->load();

		$this->assertEquals(false, $content);
	}

	public function testCacheExpiresString()
	{
		$cache = new Cache(__METHOD__, 'PT2S', $this->getHandler()); // expires in 2 seconds
		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals('foobar', $content);

		sleep(3); // wait 2 seconds

		$content = $cache->load();

		$this->assertEquals(false, $content);
	}

	public function testCacheExpiresDateInterval()
	{
		$cache = new Cache(__METHOD__, new DateInterval('PT2S'), $this->getHandler()); // expires in 2 seconds
		$cache->remove();

		$content = $cache->load();

		$this->assertEquals(false, $content);

		$cache->write('foobar');

		$content = $cache->load();

		$this->assertEquals('foobar', $content);

		sleep(3); // wait 2 seconds

		$content = $cache->load();

		$this->assertEquals(false, $content);
	}
}


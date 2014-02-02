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

namespace PSX;

use PSX\Base;

/**
 * BaseTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $base;

	protected function setUp()
	{
		$this->base = getContainer()->get('base');
	}

	protected function tearDown()
	{
		unset($this->base);
	}

	public function testGetHost()
	{
		$this->assertEquals('127.0.0.1', $this->base->getHost());
	}

	public function testGetUrn()
	{
		$this->assertEquals('urn:psx:127.0.0.1:foo:bar', $this->base->getUrn('foo', 'bar'));
	}

	public function testGetTag()
	{
		$date = new DateTime('1986-10-09 00:00:00');
		$specific = 'foo';

		$this->assertEquals('tag:127.0.0.1,1986-10-09:foo', $this->base->getTag($date, $specific));
	}

	public function testGetUUID()
	{
		$this->assertEquals('31a897ea-a0f4-53c3-922a-a72d8bf9b7e1', $this->base->getUUID('foo'));
	}

	public function testGetVersion()
	{
		// test whether the version is an "PHP-standardized" version
		$this->assertEquals(true, version_compare(Base::getVersion(), '0.0.1') > 0);
	}
}





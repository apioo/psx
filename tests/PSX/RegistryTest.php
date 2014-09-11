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

use stdClass;

/**
 * RegistryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testRegistryClear()
	{
		Registry::getInstance()->clear();

		Registry::set('foo', 'bar');

		$this->assertEquals(1, count(Registry::getInstance()));

		Registry::getInstance()->clear();

		$this->assertEquals(0, count(Registry::getInstance()));
	}

	public function testRegistryStaticGetSet()
	{
		Registry::getInstance()->clear();

		$key   = 'stdClass';
		$value = new stdClass;

		$this->assertEquals(false, Registry::has($key));

		Registry::set($key, $value);

		$this->assertEquals(true, Registry::has($key));
		$this->assertEquals($value, Registry::get($key));
	}

	public function testRegistryMethodGetSet()
	{
		Registry::getInstance()->clear();

		$key   = 'stdClass';
		$value = new stdClass;

		$this->assertEquals(false, Registry::getInstance()->offsetExists($key));

		Registry::getInstance()->offsetSet($key, $value);

		$this->assertEquals(true, Registry::getInstance()->offsetExists($key));
		$this->assertEquals($value, Registry::getInstance()->offsetGet($key));
	}

	public function testRegistryPropGetSet()
	{
		Registry::getInstance()->clear();

		$key   = 'stdClass';
		$value = new stdClass;

		$this->assertEquals(false, isset(Registry::getInstance()->$key));

		Registry::getInstance()->$key = $value;

		$this->assertEquals(true, isset(Registry::getInstance()->$key));
		$this->assertEquals($value, Registry::getInstance()->$key);
	}
}

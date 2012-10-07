<?php
/*
 *  $Id: RegistryTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_RegistryTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_RegistryTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testRegistryClear()
	{
		PSX_Registry::getInstance()->clear();

		PSX_Registry::set('foo', 'bar');

		$this->assertEquals(1, count(PSX_Registry::getInstance()));

		PSX_Registry::getInstance()->clear();

		$this->assertEquals(0, count(PSX_Registry::getInstance()));
	}

	public function testRegistryStaticGetSet()
	{
		PSX_Registry::getInstance()->clear();

		$key   = 'stdClass';
		$value = new stdClass;

		$this->assertEquals(false, PSX_Registry::has($key));

		PSX_Registry::set($key, $value);

		$this->assertEquals(true, PSX_Registry::has($key));
		$this->assertEquals($value, PSX_Registry::get($key));
	}

	public function testRegistryMethodGetSet()
	{
		PSX_Registry::getInstance()->clear();

		$key   = 'stdClass';
		$value = new stdClass;

		$this->assertEquals(false, PSX_Registry::getInstance()->offsetExists($key));

		PSX_Registry::getInstance()->offsetSet($key, $value);

		$this->assertEquals(true, PSX_Registry::getInstance()->offsetExists($key));
		$this->assertEquals($value, PSX_Registry::getInstance()->offsetGet($key));
	}

	public function testRegistryPropGetSet()
	{
		PSX_Registry::getInstance()->clear();

		$key   = 'stdClass';
		$value = new stdClass;

		$this->assertEquals(false, isset(PSX_Registry::getInstance()->$key));

		PSX_Registry::getInstance()->$key = $value;

		$this->assertEquals(true, isset(PSX_Registry::getInstance()->$key));
		$this->assertEquals($value, PSX_Registry::getInstance()->$key);
	}
}

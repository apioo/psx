<?php
/*
 *  $Id: BaseTest.php 516 2012-06-16 13:48:57Z k42b3.x@googlemail.com $
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
 * PSX_BaseTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 516 $
 */
class PSX_BaseTest extends PHPUnit_Framework_TestCase
{
	private $base;

	protected function setUp()
	{
		$config = new PSX_Config('../configuration.php');

		$this->base = PSX_Base::initInstance($config);
	}

	protected function tearDown()
	{
		unset($this->base);
	}

	public function testHeaderSent()
	{
		$this->assertEquals(false, PSX_Base::hasHeaderSent('foo'));

		/*
		// unfortunately we cannot send an header because output is already made
		header('Foo: bar');

		$this->assertEquals(true, PSX_Base::hasHeaderSent('foo'));
		*/
	}

	public function testGetConfig()
	{
		$this->assertEquals(true, $this->base->getConfig() instanceof PSX_Config);
	}

	public function testGetLoader()
	{
		$this->assertEquals(true, $this->base->getLoader() instanceof PSX_Loader);
	}

	public function testGetHost()
	{
		$this->assertEquals('127.0.0.1', $this->base->getHost());
	}

	/**
	 * @depends testGetHost
	 */
	public function testGetUrn()
	{
		$this->assertEquals('urn:psx:127.0.0.1:foo:bar', $this->base->getUrn('foo', 'bar'));
	}

	/**
	 * @depends testGetHost
	 */
	public function testGetTag()
	{
		$date = new DateTime('1986-10-09 00:00:00');
		$specific = 'foo';

		$this->assertEquals('tag:127.0.0.1,1986-10-09:foo', $this->base->getTag($date, $specific));
	}

	/**
	 * @depends testGetHost
	 */
	public function testGetUUID()
	{
		$this->assertEquals('31a897ea-a0f4-53c3-922a-a72d8bf9b7e1', $this->base->getUUID('foo'));
	}

	public function testGetVersion()
	{
		// test whether the version is an "PHP-standardized" version
		$this->assertEquals(true, version_compare(PSX_Base::getVersion(), '0.0.1') > 0);
	}
}





<?php
/*
 *  $Id: WebfingerTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_WebfingerTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_WebfingerTest extends PHPUnit_Framework_TestCase
{
	const URL  = 'http://test.phpsx.org';
	const ACCT = 'foo@foo.com';

	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testGetHostMeta()
	{
		$webfinger = new PSX_Webfinger(new PSX_Http(new PSX_Http_Handler_Curl()));
		$xrd       = $webfinger->getHostMeta(new PSX_Url(self::URL));

		$this->assertEquals($xrd instanceof PSX_Webfinger_Xrd, true);
		$this->assertEquals($xrd->getSubject(), self::URL);
	}

	public function testGetHostMetaFullUrl()
	{
		$url       = self::URL . '/foobar/test?bar=foo#test';
		$webfinger = new PSX_Webfinger(new PSX_Http(new PSX_Http_Handler_Curl()));
		$xrd       = $webfinger->getHostMeta(new PSX_Url($url));

		$this->assertEquals($xrd instanceof PSX_Webfinger_Xrd, true);
		$this->assertEquals($xrd->getSubject(), self::URL);
	}

	public function testGetLrddTemplate()
	{
		$webfinger = new PSX_Webfinger(new PSX_Http(new PSX_Http_Handler_Curl()));
		$template  = $webfinger->getLrddTemplate(new PSX_Url(self::URL));

		$this->assertEquals($template, 'http://test.phpsx.org/webfinger/lrdd?uri={uri}');
	}

	public function testGetLrdd()
	{
		$webfinger = new PSX_Webfinger(new PSX_Http(new PSX_Http_Handler_Curl()));
		$xrd       = $webfinger->getLrdd('acct:' . self::ACCT, $webfinger->getLrddTemplate(new PSX_Url(self::URL)));

		$this->assertEquals($xrd instanceof PSX_Webfinger_Xrd, true);
		$this->assertEquals($xrd->getSubject(), 'acct:' . self::ACCT);
	}

	public function testGetAcctProfile()
	{
		$webfinger = new PSX_Webfinger(new PSX_Http(new PSX_Http_Handler_Curl()));
		$profile   = $webfinger->getAcctProfile(self::ACCT, $webfinger->getLrddTemplate(new PSX_Url(self::URL)));

		$this->assertEquals($profile, 'http://test.phpsx.org/profile/foo');
	}
}


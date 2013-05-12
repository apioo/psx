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

/**
 * WebfingerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WebfingerTest extends \PHPUnit_Framework_TestCase
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
		$webfinger = new Webfinger(new Http());
		$xrd       = $webfinger->getHostMeta(new Url(self::URL));

		$this->assertEquals($xrd instanceof Webfinger\Xrd, true);
		$this->assertEquals($xrd->getSubject(), self::URL);
	}

	public function testGetHostMetaFullUrl()
	{
		$url       = self::URL . '/foobar/test?bar=foo#test';
		$webfinger = new Webfinger(new Http());
		$xrd       = $webfinger->getHostMeta(new Url($url));

		$this->assertEquals($xrd instanceof Webfinger\Xrd, true);
		$this->assertEquals($xrd->getSubject(), self::URL);
	}

	public function testGetLrddTemplate()
	{
		$webfinger = new Webfinger(new Http());
		$template  = $webfinger->getLrddTemplate(new Url(self::URL));

		$this->assertEquals($template, 'http://test.phpsx.org/webfinger/lrdd?uri={uri}');
	}

	public function testGetLrdd()
	{
		$webfinger = new Webfinger(new Http());
		$xrd       = $webfinger->getLrdd('acct:' . self::ACCT, $webfinger->getLrddTemplate(new Url(self::URL)));

		$this->assertEquals($xrd instanceof Webfinger\Xrd, true);
		$this->assertEquals($xrd->getSubject(), 'acct:' . self::ACCT);
	}

	public function testGetAcctProfile()
	{
		$webfinger = new Webfinger(new Http());
		$profile   = $webfinger->getAcctProfile(self::ACCT, $webfinger->getLrddTemplate(new Url(self::URL)));

		$this->assertEquals($profile, 'http://test.phpsx.org/profile/foo');
	}
}


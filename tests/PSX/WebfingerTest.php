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

use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;

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

	private $http;
	private $webfinger;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Webfinger/webfinger_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Webfinger/webfinger_http_fixture.xml');

		$this->http      = new Http($mock);
		$this->webfinger = new Webfinger($this->http);
	}

	protected function tearDown()
	{
		unset($this->webfinger);
		unset($this->http);
	}

	public function testGetHostMeta()
	{
		$xrd = $this->webfinger->getHostMeta(new Url(self::URL));

		$this->assertEquals($xrd instanceof Webfinger\Xrd, true);
		$this->assertEquals($xrd->getSubject(), self::URL);
	}

	public function testGetHostMetaFullUrl()
	{
		$url = self::URL . '/foobar/test?bar=foo#test';
		$xrd = $this->webfinger->getHostMeta(new Url($url));

		$this->assertEquals($xrd instanceof Webfinger\Xrd, true);
		$this->assertEquals($xrd->getSubject(), self::URL);
	}

	public function testGetLrddTemplate()
	{
		$template = $this->webfinger->getLrddTemplate(new Url(self::URL));

		$this->assertEquals($template, 'http://test.phpsx.org/webfinger/lrdd?uri={uri}');
	}

	public function testGetLrdd()
	{
		$xrd = $this->webfinger->getLrdd('acct:' . self::ACCT, $this->webfinger->getLrddTemplate(new Url(self::URL)));

		$this->assertEquals($xrd instanceof Webfinger\Xrd, true);
		$this->assertEquals($xrd->getSubject(), 'acct:' . self::ACCT);
	}

	public function testGetAcctProfile()
	{
		$profile = $this->webfinger->getAcctProfile(self::ACCT, $this->webfinger->getLrddTemplate(new Url(self::URL)));

		$this->assertEquals($profile, 'http://test.phpsx.org/profile/foo');
	}
}


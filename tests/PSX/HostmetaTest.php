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
 * HostmetaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HostmetaTest extends \PHPUnit_Framework_TestCase
{
	private $http;
	private $hostmeta;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Hostmeta/hostmeta_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Hostmeta/hostmeta_http_fixture.xml');

		$this->http     = new Http($mock);
		$this->hostmeta = new Hostmeta($this->http);
	}

	protected function tearDown()
	{
		unset($this->hostmeta);
		unset($this->http);
	}

	public function testDiscoverPumpity()
	{
		$xrd = $this->hostmeta->discover(new Url('https://pumpity.net'));

		$this->assertInstanceOf('PSX\Hostmeta\DocumentAbstract', $xrd);

		$link = $xrd->getLinkByRel('lrdd');

		$this->assertInstanceOf('PSX\Hostmeta\Link', $link);
		$this->assertEquals('lrdd', $link->getRel());
		$this->assertEquals('application/xrd+xml', $link->getType());
		$this->assertEquals('https://pumpity.net/api/lrdd?resource={uri}', $link->getTemplate());

		$link = $xrd->getLinkByRel('http://apinamespace.org/oauth/request_token');

		$this->assertInstanceOf('PSX\Hostmeta\Link', $link);
		$this->assertEquals('http://apinamespace.org/oauth/request_token', $link->getRel());
		$this->assertEquals('https://pumpity.net/oauth/request_token', $link->getHref());
	}

	public function testDiscoverGmail()
	{
		$xrd = $this->hostmeta->discover(new Url('https://gmail.com'));

		$this->assertInstanceOf('PSX\Hostmeta\DocumentAbstract', $xrd);

		$link = $xrd->getLinkByRel('lrdd');

		$this->assertInstanceOf('PSX\Hostmeta\Link', $link);
		$this->assertEquals('lrdd', $link->getRel());
		$this->assertEquals('https://profiles.google.com/_/webfinger/?q={uri}', $link->getTemplate());
	}
}


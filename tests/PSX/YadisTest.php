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
 * YadisTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class YadisTest extends \PHPUnit_Framework_TestCase
{
	const URL = 'http://test.phpsx.org';

	private $http;
	private $yadis;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Yadis/yadis_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Yadis/yadis_http_fixture.xml');

		$this->http  = new Http($mock);
		$this->yadis = new Yadis($this->http);
	}

	protected function tearDown()
	{
		unset($this->yadis);
		unset($this->http);
	}

	public function testDiscovery()
	{
		$xrd = $this->yadis->discover(new Url(self::URL));

		$this->assertEquals(true, $xrd instanceof Xrd);

		$service = current($xrd->service);
		$type    = current($service->getType());

		$this->assertEquals('http://test.phpsx.org', $service->getUri());
		$this->assertEquals('http://ns.test.phpsx.org/2011/test', $type);
	}
}

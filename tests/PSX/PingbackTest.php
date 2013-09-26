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
 * PingbackTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PingbackTest extends \PHPUnit_Framework_TestCase
{
	const SERVER_URL   = 'http://test.phpsx.org/pingback/server';
	const RESOURCE_URL = 'http://test.phpsx.org/pingback/resource';

	private $http;
	private $pingback;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Pingback/pingback_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Pingback/pingback_http_fixture.xml');

		$this->http     = new Http($mock);
		$this->pingback = new Pingback($this->http);
	}

	protected function tearDown()
	{
	}

	public function testDiscovery()
	{
		$response = $this->pingback->send('http://foobar.com', self::RESOURCE_URL);

		$this->assertEquals(true, $response);
	}
}

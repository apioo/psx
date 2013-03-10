<?php
/*
 *  $Id: RedirectTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Http;

use PSX\Http;
use PSX\Http\Handler;
use PSX\HttpTest;
use PSX\Url;

/**
 * PSX_Http_RedirectTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testRedirect()
	{
		$http    = new Http();
		$request = new GetRequest(new Url(HttpTest::URL . '/redirect'));
		$request->setFollowLocation(true);

		$response = $http->request($request);

		$this->assertEquals('SUCCESS', $response->getBody());
	}

	/**
	 * @expectedException \PSX\Http\RedirectException
	 */
	public function testMaxRedirect()
	{
		$http    = new Http();
		$request = new GetRequest(new Url(HttpTest::URL . '/redirect'));
		$request->setFollowLocation(true, 1);

		$response = $http->request($request);
	}

	public function testManualRedirect()
	{
		$handler = new Handler\Curl();
		$handler->setFollowLocation(false);
		$http    = new Http($handler);

		$request = new GetRequest(new Url(HttpTest::URL . '/redirect'));
		$request->setFollowLocation(true);

		$response = $http->request($request);

		$this->assertEquals('SUCCESS', $response->getBody());
	}

	/**
	 * @expectedException \PSX\Http\RedirectException
	 */
	public function testManualMaxRedirect()
	{
		$handler = new Handler\Curl();
		$handler->setFollowLocation(false);
		$http    = new Http($handler);

		$request = new GetRequest(new Url(HttpTest::URL . '/redirect'));
		$request->setFollowLocation(true, 1);

		$response = $http->request($request);
	}
}


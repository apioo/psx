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

/**
 * SessionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
	protected $sess;

	protected function setUp()
	{
		$this->sess = new Session('psx_session', $this->getHandler());
		$this->sess->start();
	}

	protected function tearDown()
	{
		if($this->sess instanceof Session)
		{
			$this->sess->destroy();
			$this->sess->close();
		}
	}

	protected function getHandler()
	{
		// use default session handler
		return null;
	}

	public function testGetSet()
	{
		$this->assertEquals(false, isset($_SESSION['foo']));
		$this->assertEquals(false, $this->sess->get('foo'));
		$this->assertEquals(false, $this->sess->has('foo'));

		$this->sess->set('foo', 'bar');

		$this->assertEquals(true, isset($_SESSION['foo']));
		$this->assertEquals('bar', $_SESSION['foo']);
		$this->assertEquals('bar', $this->sess->get('foo'));
		$this->assertEquals(true, $this->sess->has('foo'));
	}

	public function testPropertyGetSet()
	{
		$this->assertEquals(false, $this->sess->foo);

		$this->sess->foo = 'bar';

		$this->assertEquals('bar', $this->sess->foo);
	}

	public function testGetter()
	{
		$this->assertEquals('psx_session', $this->sess->getName());
		$this->assertEquals('PSX\Session', $this->sess->getSessionTokenKey());

		// token is always the same since we are on CLI and have no user agent
		// or remote ip
		$this->assertEquals('876d2e7b380ea3c9567ef09df11c7926', $this->sess->getToken());
	}
}

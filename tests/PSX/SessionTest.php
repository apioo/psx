<?php
/*
 *  $Id: SessionTest.php 562 2012-07-29 20:17:14Z k42b3.x@googlemail.com $
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

namespace PSX;

/**
 * PSX_SessionTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 562 $
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
	protected $sess;

	protected function setUp()
	{
		ini_set('session.use_cookies', 0);
		ini_set('session.use_only_cookies', 0);
		ini_set('session.use_trans_sid', 1);
		ini_set('session.cache_limiter', ''); // prevent sending header

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
		$this->assertEquals(false, $this->sess->foo);
		$this->assertEquals(false, $this->sess->get('foo'));

		$_SESSION['foo'] = 'bar';

		$this->assertEquals(true, isset($_SESSION['foo']));
		$this->assertEquals('bar', $_SESSION['foo']);
		$this->assertEquals('bar', $this->sess->foo);
		$this->assertEquals('bar', $this->sess->get('foo'));
	}
}

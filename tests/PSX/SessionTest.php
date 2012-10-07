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

/**
 * PSX_SessionTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 562 $
 */
class PSX_SessionTest extends PHPUnit_Framework_TestCase
{
	protected $sess;

	protected function setUp()
	{
		$this->sess = new PSX_Session(__CLASS__, $this->getHandler());

		ini_set('session.use_cookies', 0);
		ini_set('session.use_only_cookies', 0);
		ini_set('session.use_trans_sid', 1);
		ini_set('session.cache_limiter', ''); // prevent sending header

		$this->sess->start();
	}

	protected function tearDown()
	{
		if($this->sess instanceof PSX_Session)
		{
			$this->sess->close();
		}
	}

	protected function getHandler()
	{
		return null;
	}

	public function testSession()
	{
		$session = new PSX_Input_Session();

		$session->offsetSet('foo', 'bar');

		$this->assertEquals('bar', $session->foo);
		$this->assertEquals(true, $session->offsetExists('foo'));

		$session->offsetUnset('foo');

		$this->assertEquals(false, $session->offsetExists('foo'));
		$this->assertEquals(false, $session->foo);
	}
}


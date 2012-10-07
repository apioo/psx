<?php
/*
 *  $Id: UrlTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_UrlTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_UrlTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testUrl()
	{
		$url = new PSX_Url('http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker');

		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals('benutzername', $url->getUser());
		$this->assertEquals('passwort', $url->getPass());
		$this->assertEquals('hostname', $url->getHost());
		$this->assertEquals('8080', $url->getPort());
		$this->assertEquals('/pfad', $url->getPath());
		$this->assertEquals(array('argument' => 'wert'), $url->getParams());
		$this->assertEquals('textanker', $url->getFragment());
	}

	/**
	 * @expectedException PSX_Url_Exception
	 */
	public function testInvalidUrl()
	{
		new PSX_Url('foobar');
	}

	public function testUrltoString()
	{
		$urls = array(

			'http://www.yahoo.com',
			'http://www.yahoo.com/',
			'http://www.yahoo.com/foo/bar',
			'http://www.yahoo.com?foo=bar&bar=foo',
			'http://www.yahoo.com:8080',
			'http://www.yahoo.com:8080/foo/bar',
			'http://www.yahoo.com:8080?foo=bar&bar=foo',
			'http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker',

		);

		foreach($urls as $u)
		{
			$url = new PSX_Url($u);

			$this->assertEquals($u, $url->__toString());
		}
	}
}

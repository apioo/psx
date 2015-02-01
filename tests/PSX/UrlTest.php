<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
 * UrlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
	public function testUrl()
	{
		$url = new Url('http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker');

		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals('benutzername:passwort', $url->getUserInfo());
		$this->assertEquals('hostname', $url->getHost());
		$this->assertEquals('8080', $url->getPort());
		$this->assertEquals('/pfad', $url->getPath());
		$this->assertEquals(array('argument' => 'wert'), $url->getParameters());
		$this->assertEquals('textanker', $url->getFragment());
	}

	public function testUrlIpv6()
	{
		$url = new Url('http://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:80/index.html');

		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals(null, $url->getUserInfo());
		$this->assertEquals('[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]', $url->getHost());
		$this->assertEquals(80, $url->getPort());
		$this->assertEquals('/index.html', $url->getPath());
		$this->assertEquals(array(), $url->getParameters());
		$this->assertEquals(null, $url->getFragment());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidUrl()
	{
		new Url('foobar');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHost()
	{
		new Url('foo://');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHostButPath()
	{
		new Url('foo:///foo');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHostButQuery()
	{
		new Url('foo://?foo=bar');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHostButFragment()
	{
		new Url('foo://#foo');
	}

	public function testSetPortValidRange()
	{
		$uri = new Url('http://www.yahoo.com/');

		for($i = 0; $i < 20; $i++)
		{
			$port = rand(1, 0xFFFF);

			$uri->setPort($port);

			$this->assertEquals('http://www.yahoo.com:' . $port . '/', $uri->__toString());
		}
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetPortInvalidRangeMin()
	{
		$uri = new Url('http://www.yahoo.com/');
		$uri->setPort(-1);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetPortInvalidRangeMax()
	{
		$uri = new Url('http://www.yahoo.com/');
		$uri->setPort(0xFFFF + 1);
	}

	public function testShortUrls()
	{
		$url = new Url('//www.yahoo.com');

		$this->assertEquals('http://www.yahoo.com', $url->__toString());
	}

	public function testUrlWithoutFile()
	{
		$url = new Url('http://127.0.0.1/projects/foo/bar/?project=symfony%2Fsymfony&source=1&destination=2');

		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals(null, $url->getUserInfo());
		$this->assertEquals('127.0.0.1', $url->getHost());
		$this->assertEquals(null, $url->getPort());
		$this->assertEquals('/projects/foo/bar/', $url->getPath());
		$this->assertEquals(array('project' => 'symfony/symfony', 'source' => '1', 'destination' => '2'), $url->getParameters());
		$this->assertEquals(null, $url->getFragment());
	}

	public function testUrlFragmentEncoding()
	{
		$url = new Url('http://127.0.0.1/foobar?bar=foo#!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~');

		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals(null, $url->getUserInfo());
		$this->assertEquals('127.0.0.1', $url->getHost());
		$this->assertEquals(null, $url->getPort());
		$this->assertEquals('/foobar', $url->getPath());
		$this->assertEquals(array('bar' => 'foo'), $url->getParameters());
		$this->assertEquals('!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~', $url->getFragment());
	}
}

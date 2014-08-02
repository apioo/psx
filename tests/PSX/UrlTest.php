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
 * UrlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testUrl()
	{
		$url = new Url('http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker');

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
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidUrl()
	{
		new Url('foobar');
	}

	public function testToString()
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
			$url = new Url($u);

			$this->assertEquals($u, $url->__toString());
		}
	}

	public function testShortUrls()
	{
		$url = new Url('//www.yahoo.com');

		$this->assertEquals('http://www.yahoo.com', $url->__toString());
	}

	public function testGetParams()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');

		$this->assertEquals(array('bar' => 'foo', 'foo' => 'bar'), $url->getParams());
	}

	public function testAddParams()
	{
		$url = new Url('http://foo.com');
		$url->addParams(array('bar' => 'foo', 'foo' => 'bar'));

		$this->assertEquals(array('bar' => 'foo', 'foo' => 'bar'), $url->getParams());
	}

	public function testGetParam()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');

		$this->assertEquals('foo', $url->getParam('bar'));
	}

	public function testSetParam()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');
		$url->setParam('bar', 'test');

		$this->assertEquals('test', $url->getParam('bar'));
	}

	public function testAddParam()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');
		$url->addParam('bar', 'test');

		$this->assertEquals('foo', $url->getParam('bar'));
	}

	public function testAddParamReplace()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');
		$url->addParam('bar', 'test', true);

		$this->assertEquals('test', $url->getParam('bar'));
	}

	public function testDeleteParam()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');
		$url->deleteParam('bar');

		$this->assertEquals(null, $url->getParam('bar'));
	}

	public function testIssetParam()
	{
		$url = new Url('http://foo.com?bar=foo&foo=bar');

		$this->assertTrue($url->issetParam('bar'));
	}

	public function testUrlWithoutFile()
	{
		$url = new Url('http://127.0.0.1/projects/foo/bar/?project=symfony%2Fsymfony&source=1&destination=2');

		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals(null, $url->getUser());
		$this->assertEquals(null, $url->getPass());
		$this->assertEquals('127.0.0.1', $url->getHost());
		$this->assertEquals(null, $url->getPort());
		$this->assertEquals('/projects/foo/bar/', $url->getPath());
		$this->assertEquals(array('project' => 'symfony/symfony', 'source' => '1', 'destination' => '2'), $url->getParams());
		$this->assertEquals(null, $url->getFragment());
	}
}

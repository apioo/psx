<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX;

/**
 * UrlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidUrl()
	{
		new Url('foobar');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHost()
	{
		new Url('foo://');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHostButPath()
	{
		new Url('foo:///foo');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHostButQuery()
	{
		new Url('foo://?foo=bar');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidUrlEmptyHostButFragment()
	{
		new Url('foo://#foo');
	}

	public function testPort()
	{
		$uri = new Url('http://www.yahoo.com:8080/');

		$this->assertEquals('http://www.yahoo.com:8080/', $uri->toString());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetPortInvalidRangeMin()
	{
		$port = -1;
		$uri  = new Url('http://www.yahoo.com:' . $port . '/');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetPortInvalidRangeMax()
	{
		$port = 0xFFFF + 1;
		$uri  = new Url('http://www.yahoo.com:' . $port . '/');
	}

	public function testShortUrls()
	{
		$url = new Url('//www.yahoo.com');

		$this->assertEquals('http://www.yahoo.com', $url->toString());
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

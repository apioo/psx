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

namespace PSX\Dispatch;

use PSX\Config;

/**
 * The most difficult task of the request factory is to recreate the request uri
 * from the server environment vars since its not always sure which vars are 
 * available. We assume the webserver follows the rfc3875
 *
 * @see     http://www.ietf.org/rfc/rfc3875
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateRequest()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$matrix = array(
			'http://foo.com' => array('PATH_INFO' => null, 'QUERY_STRING' => null),
			'http://foo.com/' => array('PATH_INFO' => null, 'QUERY_STRING' => null),
			'http://foo.com/' => array('PATH_INFO' => '/', 'QUERY_STRING' => null),
			'http://foo.com/bar' => array('PATH_INFO' => '/bar', 'QUERY_STRING' => null),
			'http://foo.com/bar?bar=test' => array('PATH_INFO' => '/bar', 'QUERY_STRING' => 'bar=test'),
			'http://foo.com?bar=test' => array('PATH_INFO' => null, 'QUERY_STRING' => 'bar=test'),
		);

		foreach($matrix as $uri => $env)
		{
			$request = $this->getRequest($env, $config);

			$this->assertEquals($uri, (string) $request->getUrl());
		}
	}

	public function testCreateRequestInCli()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$factory = $this->getMockBuilder('PSX\Dispatch\RequestFactory')
			->setConstructorArgs(array($config))
			->setMethods(array('isCli'))
			->getMock();

		$factory->expects($this->once())
			->method('isCli')
			->will($this->returnValue(true));

		$_SERVER['argv'][1] = '/foo';

		$this->assertEquals('http://foo.com/foo', (string) $factory->createRequest()->getUrl());
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testCreateRequestInvalidUrl()
	{
		$config = new Config(array(
			'psx_url' => 'foobar',
		));

		$factory = new RequestFactory($config);
		$factory->createRequest();
	}

	public function testGetRequestMethod()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('REQUEST_METHOD' => 'POST');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('POST', $request->getMethod());
	}

	public function testGetRequestMethodOverwrite()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'PUT');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('PUT', $request->getMethod());
	}

	public function testGetRequestMethodOverwriteInvalid()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'FOO');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('POST', $request->getMethod());
	}

	public function testGetRequestHeader()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_FOO_BAR' => 'foobar');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
	}

	public function testSoapActionHeader()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_SOAPACTION' => 'http://foobar.com/api/method#GET');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('application/soap+xml', $request->getHeader('Content-Type'));
		$this->assertEquals('application/soap+xml', $request->getHeader('Accept'));
		$this->assertEquals('http://foobar.com/api/method#GET', $request->getHeader('Soapaction'));
	}

	public function testGetRequestHeaderContentHeader()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_FOO_BAR' => 'foobar', 'CONTENT_LENGTH' => 8, 'CONTENT_MD5' => 'foobar', 'CONTENT_TYPE' => 'text/html');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
		$this->assertEquals(8, $request->getHeader('Content-Length'));
		$this->assertEquals('foobar', $request->getHeader('Content-MD5'));
		$this->assertEquals('text/html', $request->getHeader('Content-Type'));
	}

	public function testGetRequestHeaderRedirectAuthorizationHeader()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_FOO_BAR' => 'foobar', 'REDIRECT_HTTP_AUTHORIZATION' => 'Basic Zm9vOmJhcg==');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
		$this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
	}

	public function testGetRequestHeaderPhpAuthUser()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => 'bar');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
		$this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
	}

	public function testGetRequestHeaderPhpAuthUserNoPw()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => null);

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
		$this->assertEquals('Basic Zm9vOg==', $request->getHeader('Authorization'));
	}

	public function testGetRequestHeaderDigest()
	{
		$config = new Config(array(
			'psx_url' => 'http://foo.com',
		));

		$env = array('HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_DIGEST' => 'Digest foobar');

		$_SERVER['argv'][1] = '/';

		$request = $this->getRequest($env, $config, true);

		$this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
		$this->assertEquals('Digest foobar', $request->getHeader('Authorization'));
	}

	protected function getRequest(array $env, Config $config, $isCli = false)
	{
		$factory = $this->getMockBuilder('PSX\Dispatch\RequestFactory')
			->setConstructorArgs(array($config))
			->setMethods(array('isCli'))
			->getMock();

		$factory->expects($this->once())
			->method('isCli')
			->will($this->returnValue($isCli));

		foreach($env as $key => $value)
		{
			$_SERVER[$key] = $value;
		}

		return $factory->createRequest();
	}
}

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
 * UriTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
	public function testRfcExample1()
	{
		$uri = new Uri('ftp://ftp.is.co.za/rfc/rfc1808.txt');

		$this->assertEquals('ftp', $uri->getScheme());
		$this->assertEquals('ftp.is.co.za', $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals('ftp.is.co.za', $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('/rfc/rfc1808.txt', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('ftp://ftp.is.co.za/rfc/rfc1808.txt', $uri->toString());
	}

	public function testRfcExample2()
	{
		$uri = new Uri('http://www.ietf.org/rfc/rfc2396.txt');

		$this->assertEquals('http', $uri->getScheme());
		$this->assertEquals('www.ietf.org', $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals('www.ietf.org', $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('/rfc/rfc2396.txt', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('http://www.ietf.org/rfc/rfc2396.txt', $uri->toString());
	}

	public function testRfcExample3()
	{
		$uri = new Uri('ldap://[2001:db8::7]/c=GB?objectClass?one');

		$this->assertEquals('ldap', $uri->getScheme());
		$this->assertEquals('[2001:db8::7]', $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals('[2001:db8::7]', $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('/c=GB', $uri->getPath());
		$this->assertEquals('objectClass?one', $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('ldap://[2001:db8::7]/c=GB?objectClass?one', $uri->toString());
	}

	public function testRfcExample3_1()
	{
		$uri = new Uri('ldap://[2001:db8::7]:80/c=GB?objectClass?one');

		$this->assertEquals('ldap', $uri->getScheme());
		$this->assertEquals('[2001:db8::7]:80', $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals('[2001:db8::7]', $uri->getHost());
		$this->assertEquals(80, $uri->getPort());
		$this->assertEquals('/c=GB', $uri->getPath());
		$this->assertEquals('objectClass?one', $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('ldap://[2001:db8::7]:80/c=GB?objectClass?one', $uri->toString());
	}

	public function testRfcExample4()
	{
		$uri = new Uri('mailto:John.Doe@example.com');

		$this->assertEquals('mailto', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals(null, $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('John.Doe@example.com', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('mailto:John.Doe@example.com', $uri->toString());
	}

	public function testRfcExample5()
	{
		$uri = new Uri('mailto://John.Doe@example.com');

		$this->assertEquals('mailto', $uri->getScheme());
		$this->assertEquals('John.Doe@example.com', $uri->getAuthority());
		$this->assertEquals('John.Doe', $uri->getUserInfo());
		$this->assertEquals('example.com', $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals(null, $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('mailto://John.Doe@example.com', $uri->toString());
	}

	public function testRfcExample6()
	{
		$uri = new Uri('news:comp.infosystems.www.servers.unix');

		$this->assertEquals('news', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals(null, $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('comp.infosystems.www.servers.unix', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('news:comp.infosystems.www.servers.unix', $uri->toString());
	}

	public function testRfcExample7()
	{
		$uri = new Uri('tel:+1-816-555-1212');

		$this->assertEquals('tel', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals(null, $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('+1-816-555-1212', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('tel:+1-816-555-1212', $uri->toString());
	}

	public function testRfcExample8()
	{
		$uri = new Uri('telnet://192.0.2.16:80/');

		$this->assertEquals('telnet', $uri->getScheme());
		$this->assertEquals('192.0.2.16:80', $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals('192.0.2.16', $uri->getHost());
		$this->assertEquals(80, $uri->getPort());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('telnet://192.0.2.16:80/', $uri->toString());
	}

	public function testRfcExample9()
	{
		$uri = new Uri('urn:oasis:names:specification:docbook:dtd:xml:4.1.2');

		$this->assertEquals('urn', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals(null, $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('urn:oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->toString());
	}

	public function testFull()
	{
		$uri = new Uri('foo://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

		$this->assertEquals('foo', $uri->getScheme());
		$this->assertEquals('user:password@example.com:8042', $uri->getAuthority());
		$this->assertEquals('user:password', $uri->getUserInfo());
		$this->assertEquals('example.com', $uri->getHost());
		$this->assertEquals(8042, $uri->getPort());
		$this->assertEquals('/over/there', $uri->getPath());
		$this->assertEquals('name=ferret&foo=bar', $uri->getQuery());
		$this->assertEquals('nose', $uri->getFragment());
		$this->assertEquals('foo://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose', $uri->toString());
	}

	public function testRelativeUrl()
	{
		$uri = new Uri('/foo/bar?param=value');

		$this->assertEquals(null, $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals(null, $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('/foo/bar', $uri->getPath());
		$this->assertEquals('param=value', $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('/foo/bar?param=value', $uri->toString());
	}

	public function testUrlNoScheme()
	{
		$uri = new Uri('//google.com/foo/bar?foo=bar');

		$this->assertEquals(null, $uri->getScheme());
		$this->assertEquals('google.com', $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals('google.com', $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('/foo/bar', $uri->getPath());
		$this->assertEquals('foo=bar', $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('//google.com/foo/bar?foo=bar', $uri->toString());
	}

	public function testFileWindowsScheme()
	{
		$uri = new Uri('file:///c:/somedir/file.txt');

		$this->assertEquals('file', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals(null, $uri->getUserInfo());
		$this->assertEquals(null, $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('/c:/somedir/file.txt', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
		$this->assertEquals('file:/c:/somedir/file.txt', $uri->toString());
	}

	public function testEmptyString()
	{
		$uri = new Uri('');

		$this->assertEquals('', $uri->toString());
	}

	public function testNonEmptyString()
	{
		$uri = new Uri('foo');

		$this->assertEquals('foo', $uri->toString());
	}

	public function testNullValue()
	{
		$uri = new Uri(null);

		$this->assertEquals(null, $uri->toString());
	}

	/**
	 * Test to check whether we can materialize the URI into an string form 
	 * without loosing any information
	 *
	 * @dataProvider toStringProvider
	 */
	public function testToString($rawUri)
	{
		$uri = new Uri($rawUri);

		$this->assertEquals($rawUri, $uri->toString(), $rawUri);
	}

	public function toStringProvider()
	{
		return array(
			['//www.yahoo.com'],
			['/foo/bar/index.php?bar=test'],
			['http://www.yahoo.com'],
			['http://www.yahoo.com/'],
			['http://www.yahoo.com/foo/bar'],
			['http://www.yahoo.com?foo=bar&bar=foo'],
			['http://www.yahoo.com:8080'],
			['http://www.yahoo.com:8080/foo/bar'],
			['http://www.yahoo.com:8080?foo=bar&bar=foo'],
			['http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker'],
			['ftp://ftp.is.co.za/rfc/rfc1808.txt'],
			['http://www.ietf.org/rfc/rfc2396.txt'],
			['ldap://[2001:db8::7]/c=GB?objectClass?one'],
			['mailto:John.Doe@example.com'],
			['news:comp.infosystems.www.servers.unix'],
			['tel:+1-816-555-1212'],
			['telnet://192.0.2.16:80/'],
			['urn:oasis:names:specification:docbook:dtd:xml:4.1.2'],
		);
	}

	public function testEquals()
	{
		$uris = array(
			'http://abc.com:/~smith/home.html',
			'http://ABC.com/%7Esmith/home.html',
			'http://ABC.com:/%7esmith/home.html'
		);

		foreach($uris as $uri)
		{
			$sourceUri = new Uri($uri);

			foreach($uris as $uri)
			{
				$this->assertTrue($sourceUri->equals($uri), $uri . ' == ' . $sourceUri);
			}
		}
	}

	public function testSetScheme()
	{
		$uri = new Uri('http://www.yahoo.com/');
		
		$this->assertEquals('http', $uri->getScheme());

		$uri->setScheme('https');

		$this->assertEquals('https', $uri->getScheme());
		$this->assertEquals('https://www.yahoo.com/', $uri->toString());
	}

	public function testSetAuthority()
	{
		$uri = new Uri('http://www.yahoo.com/');
		
		$this->assertEquals('www.yahoo.com', $uri->getAuthority());

		$uri->setAuthority('yahoo.com:8080');

		$this->assertEquals('yahoo.com:8080', $uri->getAuthority());
		$this->assertEquals('yahoo.com', $uri->getHost());
		$this->assertEquals(8080, $uri->getPort());
		$this->assertEquals('http://yahoo.com:8080/', $uri->toString());
	}

	public function testSetHost()
	{
		$uri = new Uri('http://www.yahoo.com/');
		
		$this->assertEquals('www.yahoo.com', $uri->getHost());

		$uri->setHost('google.com');

		$this->assertEquals('google.com', $uri->getAuthority());
		$this->assertEquals('google.com', $uri->getHost());
		$this->assertEquals(null, $uri->getPort());
		$this->assertEquals('http://google.com/', $uri->toString());
	}

	public function testSetPort()
	{
		$uri = new Uri('http://www.yahoo.com/');
		
		$this->assertEquals(null, $uri->getPort());

		$uri->setPort(8080);

		$this->assertEquals('www.yahoo.com:8080', $uri->getAuthority());
		$this->assertEquals('www.yahoo.com', $uri->getHost());
		$this->assertEquals(8080, $uri->getPort());
		$this->assertEquals('http://www.yahoo.com:8080/', $uri->toString());
	}

	public function testSetPath()
	{
		$uri = new Uri('http://www.yahoo.com/');

		$this->assertEquals('/', $uri->getPath());

		$uri->setPath('/foo');

		$this->assertEquals('/foo', $uri->getPath());
		$this->assertEquals('http://www.yahoo.com/foo', $uri->toString());
	}

	public function testSetQuery()
	{
		$uri = new Uri('http://www.yahoo.com/');
		
		$this->assertEquals(null, $uri->getQuery());

		$uri->setQuery('foo=bar&bar=foo');

		$this->assertEquals('foo=bar&bar=foo', $uri->getQuery());
		$this->assertEquals('http://www.yahoo.com/?foo=bar&bar=foo', $uri->toString());
	}

	public function testSetFragment()
	{
		$uri = new Uri('http://www.yahoo.com/');

		$this->assertEquals(null, $uri->getFragment());

		$uri->setFragment('foo');

		$this->assertEquals('foo', $uri->getFragment());
		$this->assertEquals('http://www.yahoo.com/#foo', $uri->toString());
	}
}


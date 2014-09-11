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
 * UriTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
	public function testUri()
	{
		$uri = new Uri('foo://example.com:8042/over/there?name=ferret#nose');

		$this->assertEquals('foo', $uri->getScheme());
		$this->assertEquals('example.com:8042', $uri->getAuthority());
		$this->assertEquals('/over/there', $uri->getPath());
		$this->assertEquals('name=ferret', $uri->getQuery());
		$this->assertEquals('nose', $uri->getFragment());

		$uri = new Uri('ftp://ftp.is.co.za/rfc/rfc1808.txt');

		$this->assertEquals('ftp', $uri->getScheme());
		$this->assertEquals('ftp.is.co.za', $uri->getAuthority());
		$this->assertEquals('/rfc/rfc1808.txt', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('http://www.ietf.org/rfc/rfc2396.txt');

		$this->assertEquals('http', $uri->getScheme());
		$this->assertEquals('www.ietf.org', $uri->getAuthority());
		$this->assertEquals('/rfc/rfc2396.txt', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('ldap://[2001:db8::7]/c=GB?objectClass?one');

		$this->assertEquals('ldap', $uri->getScheme());
		$this->assertEquals('[2001:db8::7]', $uri->getAuthority());
		$this->assertEquals('/c=GB', $uri->getPath());
		$this->assertEquals('objectClass?one', $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('mailto:John.Doe@example.com');

		$this->assertEquals('mailto', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals('John.Doe@example.com', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('mailto://John.Doe@example.com');

		$this->assertEquals('mailto', $uri->getScheme());
		$this->assertEquals('John.Doe@example.com', $uri->getAuthority());
		$this->assertEquals(null, $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('news:comp.infosystems.www.servers.unix');

		$this->assertEquals('news', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals('comp.infosystems.www.servers.unix', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('tel:+1-816-555-1212');

		$this->assertEquals('tel', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals('+1-816-555-1212', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('telnet://192.0.2.16:80/');

		$this->assertEquals('telnet', $uri->getScheme());
		$this->assertEquals('192.0.2.16:80', $uri->getAuthority());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());

		$uri = new Uri('urn:oasis:names:specification:docbook:dtd:xml:4.1.2');

		$this->assertEquals('urn', $uri->getScheme());
		$this->assertEquals(null, $uri->getAuthority());
		$this->assertEquals('oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->getPath());
		$this->assertEquals(null, $uri->getQuery());
		$this->assertEquals(null, $uri->getFragment());
	}

	public function testToString()
	{
		$uris = array(
			'http://www.yahoo.com',
			'http://www.yahoo.com/',
			'http://www.yahoo.com/foo/bar',
			'http://www.yahoo.com?foo=bar&bar=foo',
			'http://www.yahoo.com:8080',
			'http://www.yahoo.com:8080/foo/bar',
			'http://www.yahoo.com:8080?foo=bar&bar=foo',
			'http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker',
		);

		foreach($uris as $u)
		{
			$uri = new Uri($u);

			$this->assertEquals($u, $uri->__toString());
		}
	}

	public function testRemoveDotSegments()
	{
		$r = Uri::removeDotSegments('/a/b/c/./../../g');
		$e = '/a/g';

		$this->assertEquals($e, $r);
	}

	public function testBuildTag()
	{
		$this->assertEquals('tag:foo,2013-08-02:blog#1', Uri::buildTag('foo', new \DateTime('2013-08-02'), 'blog', '1'));
	}

	public function testPercentEncode()
	{
		$this->assertEquals('foobar', Uri::percentEncode('foobar'));
		$this->assertEquals('http%3A%2F%2Fgoogle.de', Uri::percentEncode('http://google.de'));
		$this->assertEquals('http%3A%2F%2Fgoogle.de', Uri::percentEncode('http%3A%2F%2Fgoogle.de'));
		$this->assertEquals('http%253A%252F%252Fgoogle.de', Uri::percentEncode('http%3A%2F%2Fgoogle.de', false));
	}
}


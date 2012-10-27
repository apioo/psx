<?php
/*
 *  $Id: UriTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_UriTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_UriTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testUri()
	{
		$uri = new PSX_Uri('foo://example.com:8042/over/there?name=ferret#nose');

		$this->assertEquals('foo', $uri->getScheme());
		$this->assertEquals('example.com:8042', $uri->getAuthority());
		$this->assertEquals('/over/there', $uri->getPath());
		$this->assertEquals('name=ferret', $uri->getQuery());
		$this->assertEquals('nose', $uri->getFragment());
	}

	public function testParseUri()
	{
		$r = PSX_Uri::parse('foo://example.com:8042/over/there?name=ferret#nose');
		$e = array(
			'scheme'    => 'foo',
			'authority' => 'example.com:8042',
			'path'      => '/over/there',
			'query'     => 'name=ferret',
			'fragment'  => 'nose',
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('ftp://ftp.is.co.za/rfc/rfc1808.txt');
		$e = array(
			'scheme'    => 'ftp',
			'authority' => 'ftp.is.co.za',
			'path'      => '/rfc/rfc1808.txt',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('http://www.ietf.org/rfc/rfc2396.txt');
		$e = array(
			'scheme'    => 'http',
			'authority' => 'www.ietf.org',
			'path'      => '/rfc/rfc2396.txt',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('ldap://[2001:db8::7]/c=GB?objectClass?one');
		$e = array(
			'scheme'    => 'ldap',
			'authority' => '[2001:db8::7]',
			'path'      => '/c=GB',
			'query'     => 'objectClass?one',
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('mailto:John.Doe@example.com');
		$e = array(
			'scheme'    => 'mailto',
			'authority' => null,
			'path'      => 'John.Doe@example.com',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('mailto://John.Doe@example.com');
		$e = array(
			'scheme'    => 'mailto',
			'authority' => 'John.Doe@example.com',
			'path'      => null,
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('news:comp.infosystems.www.servers.unix');
		$e = array(
			'scheme'    => 'news',
			'authority' => null,
			'path'      => 'comp.infosystems.www.servers.unix',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('tel:+1-816-555-1212');
		$e = array(
			'scheme'    => 'tel',
			'authority' => null,
			'path'      => '+1-816-555-1212',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('telnet://192.0.2.16:80/');
		$e = array(
			'scheme'    => 'telnet',
			'authority' => '192.0.2.16:80',
			'path'      => '/',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);

		$r = PSX_Uri::parse('urn:oasis:names:specification:docbook:dtd:xml:4.1.2');
		$e = array(
			'scheme'    => 'urn',
			'authority' => null,
			'path'      => 'oasis:names:specification:docbook:dtd:xml:4.1.2',
			'query'     => null,
			'fragment'  => null,
		);

		$this->assertEquals($e, $r);
	}

	public function testRemoveDotSegments()
	{
		$r = PSX_Uri::removeDotSegments('/a/b/c/./../../g');
		$e = '/a/g';

		$this->assertEquals($e, $r);
	}

	public function testPercentEncode()
	{
		$this->assertEquals('foobar', PSX_Uri::percentEncode('foobar'));
		$this->assertEquals('http%3A%2F%2Fgoogle.de', PSX_Uri::percentEncode('http://google.de'));
		$this->assertEquals('http%3A%2F%2Fgoogle.de', PSX_Uri::percentEncode('http%3A%2F%2Fgoogle.de'));
		$this->assertEquals('http%253A%252F%252Fgoogle.de', PSX_Uri::percentEncode('http%3A%2F%2Fgoogle.de', false));
	}
}


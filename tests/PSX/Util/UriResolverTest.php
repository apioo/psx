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

namespace PSX\Util;

use PSX\Uri;

/**
 * UriResolverTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UriResolverTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider uriResolveNormalProvider
	 * @see https://tools.ietf.org/html/rfc3986#section-5.4.1
	 */
	public function testResolveNormal($targetUri, $expected)
	{
		$baseUri   = new Uri('http://a/b/c/d;p?q');
		$targetUri = new Uri($targetUri);

		$this->assertEquals($expected, UriResolver::resolve($baseUri, $targetUri)->toString());
	}

	public function uriResolveNormalProvider()
	{
		return array(
			['g:h', 'g:h'],
			['g', 'http://a/b/c/g'],
			['./g', 'http://a/b/c/g'],
			['g/', 'http://a/b/c/g/'],
			['/g', 'http://a/g'],
			['//g', 'http://g'],
			['?y', 'http://a/b/c/?y'],
			['g?y', 'http://a/b/c/g?y'],
			['#s', 'http://a/b/c/d;p?q#s'],
			['g#s', 'http://a/b/c/g#s'],
			['g?y#s', 'http://a/b/c/g?y#s'],
			[';x', 'http://a/b/c/;x'],
			['g;x', 'http://a/b/c/g;x'],
			['g;x?y#s', 'http://a/b/c/g;x?y#s'],
			['.', 'http://a/b/c/'],
			['./', 'http://a/b/c/'],
			['..', 'http://a/b/'],
			['../', 'http://a/b/'],
			['../g', 'http://a/b/g'],
			['../..', 'http://a/'],
			['../../', 'http://a/'],
			['../../g', 'http://a/g'],
		);
	}

	/**
	 * @dataProvider uriResolveAbnormalProvider
	 * @see https://tools.ietf.org/html/rfc3986#section-5.4.2
	 */
	public function testResolveAbnormal($targetUri, $expected)
	{
		$baseUri   = new Uri('http://a/b/c/d;p?q');
		$targetUri = new Uri($targetUri);

		$this->assertEquals($expected, UriResolver::resolve($baseUri, $targetUri)->toString());
	}

	public function uriResolveAbnormalProvider()
	{
		return array(
			['../../../g', 'http://a/g'],
			['../../../../g', 'http://a/g'],
			['/./g', 'http://a/g'],
			['/../g', 'http://a/g'],
			['g.', 'http://a/b/c/g.'],
			['.g', 'http://a/b/c/.g'],
			['g..', 'http://a/b/c/g..'],
			['..g', 'http://a/b/c/..g'],
			['./../g', 'http://a/b/g'],
			['./g/.', 'http://a/b/c/g/'],
			['g/./h', 'http://a/b/c/g/h'],
			['g/../h', 'http://a/b/c/h'],
			['g;x=1/./y', 'http://a/b/c/g;x=1/y'],
			['g;x=1/../y', 'http://a/b/c/y'],
			['g?y/./x', 'http://a/b/c/g?y/./x'],
			['g?y/../x', 'http://a/b/c/g?y/../x'],
			['g#s/./x', 'http://a/b/c/g#s/./x'],
			['g#s/../x', 'http://a/b/c/g#s/../x'],
			['http:g', 'http:g'],
		);
	}

	public function testRemoveDotSegments()
	{
		$r = UriResolver::removeDotSegments('/a/b/c/./../../g');
		$e = '/a/g';

		$this->assertEquals($e, $r);
	}

	public function testPercentEncode()
	{
		$this->assertEquals('foobar', UriResolver::percentEncode('foobar'));
		$this->assertEquals('http%3A%2F%2Fgoogle.de', UriResolver::percentEncode('http://google.de'));
		$this->assertEquals('http%3A%2F%2Fgoogle.de', UriResolver::percentEncode('http%3A%2F%2Fgoogle.de'));
		$this->assertEquals('http%253A%252F%252Fgoogle.de', UriResolver::percentEncode('http%3A%2F%2Fgoogle.de', false));
	}
}

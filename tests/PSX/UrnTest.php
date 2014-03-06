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
 * UrnTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UrnTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testUrn()
	{
		$urn = new Urn('urn:uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6');

		$this->assertEquals('urn', $urn->getScheme());
		$this->assertEquals('uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6', $urn->getPath());
		$this->assertEquals('f81d4fae-7dec-11d0-a765-00a0c91e6bf6', $urn->getNss());
	}

	public function testBuidUrn()
	{
		$this->assertEquals('urn:foo:bar', Urn::buildUrn(array('foo', 'bar')));
	}

	/**
	 * @expectedException \PSX\Exception
	 */
	public function testInvalidUrn()
	{
		new Urn('foobar');
	}

	public function testUrnCompare()
	{
		$urns = array(
			'URN:foo:a123,456',
			'urn:foo:a123,456',
			'urn:FOO:a123,456',
			'urn:foo:A123,456',
			'urn:foo:a123%2C456',
			'URN:FOO:a123%2c456',
		);

		foreach($urns as $rawUrn)
		{
			$urn = new Urn($rawUrn);

			$this->assertEquals('urn:foo:a123,456', $urn->__toString());
		}
	}
}


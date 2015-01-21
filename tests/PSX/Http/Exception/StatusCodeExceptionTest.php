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

namespace PSX\Http\Exception;

/**
 * StatusCodeExceptionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class StatusCodeExceptionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidStatusCode()
	{
		new StatusCodeException('foo', 108);
	}

	public function testGetStatusCode()
	{
		$e = new StatusCodeException('foo', 101);

		$this->assertEquals(101, $e->getStatusCode());
	}

	public function testIsInformational()
	{
		$e = new StatusCodeException('foo', 100);

		$this->assertTrue($e->isInformational());
		$this->assertFalse($e->isSuccessful());
		$this->assertFalse($e->isRedirection());
		$this->assertFalse($e->isClientError());
		$this->assertFalse($e->isServerError());
	}

	public function testIsSuccessful()
	{
		$e = new StatusCodeException('foo', 200);

		$this->assertFalse($e->isInformational());
		$this->assertTrue($e->isSuccessful());
		$this->assertFalse($e->isRedirection());
		$this->assertFalse($e->isClientError());
		$this->assertFalse($e->isServerError());
	}

	public function testIsRedirection()
	{
		$e = new StatusCodeException('foo', 300);

		$this->assertFalse($e->isInformational());
		$this->assertFalse($e->isSuccessful());
		$this->assertTrue($e->isRedirection());
		$this->assertFalse($e->isClientError());
		$this->assertFalse($e->isServerError());
	}

	public function testIsClientError()
	{
		$e = new StatusCodeException('foo', 400);

		$this->assertFalse($e->isInformational());
		$this->assertFalse($e->isSuccessful());
		$this->assertFalse($e->isRedirection());
		$this->assertTrue($e->isClientError());
		$this->assertFalse($e->isServerError());
	}

	public function testIsServerError()
	{
		$e = new StatusCodeException('foo', 500);

		$this->assertFalse($e->isInformational());
		$this->assertFalse($e->isSuccessful());
		$this->assertFalse($e->isRedirection());
		$this->assertFalse($e->isClientError());
		$this->assertTrue($e->isServerError());
	}
}

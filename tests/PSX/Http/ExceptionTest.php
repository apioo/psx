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

namespace PSX\Http;

/**
 * ExceptionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
	public function testBadRequestException()
	{
		$this->assertRedirection(301, 'PSX\Http\Exception\MovedPermanentlyException', array('http://google.com'));
		$this->assertRedirection(302, 'PSX\Http\Exception\FoundException', array('http://google.com'));
		$this->assertRedirection(303, 'PSX\Http\Exception\SeeOtherException', array('http://google.com'));
		$this->assertRedirection(304, 'PSX\Http\Exception\NotModifiedException');

		$this->assertClientError(400, 'PSX\Http\Exception\BadRequestException', array('foo'));
		$this->assertClientError(401, 'PSX\Http\Exception\UnauthorizedException', array('foo', 'Basic', array('realm' => 'psx')));
		$this->assertClientError(403, 'PSX\Http\Exception\ForbiddenException', array('foo'));
		$this->assertClientError(404, 'PSX\Http\Exception\NotFoundException', array('foo'));
		$this->assertClientError(405, 'PSX\Http\Exception\MethodNotAllowedException', array('foo', array('GET', 'POST')));
		$this->assertClientError(406, 'PSX\Http\Exception\NotAcceptableException', array('foo'));
		$this->assertClientError(409, 'PSX\Http\Exception\ConflictException', array('foo'));
		$this->assertClientError(410, 'PSX\Http\Exception\GoneException', array('foo'));
		$this->assertClientError(415, 'PSX\Http\Exception\UnsupportedMediaTypeException', array('foo'));

		$this->assertServerError(500, 'PSX\Http\Exception\InternalServerErrorException', array('foo'));
		$this->assertServerError(501, 'PSX\Http\Exception\NotImplementedException', array('foo'));
		$this->assertServerError(503, 'PSX\Http\Exception\ServiceUnavailableException', array('foo'));
	}

	protected function assertRedirection($statusCode, $className, array $arguments = array())
	{
		$e = $this->getException($className, $arguments);

		$this->assertInstanceOf('PSX\Http\Exception\RedirectionException', $e);
		$this->assertGreaterThanOrEqual(300, $e->getStatusCode());
		$this->assertLessThan(400, $e->getStatusCode());
		$this->assertEquals($statusCode, $e->getStatusCode());

		if($statusCode != 304)
		{
			$this->assertEquals('http://google.com', $e->getLocation());
		}
	}

	protected function assertClientError($statusCode, $className, array $arguments = array())
	{
		$e = $this->getException($className, $arguments);

		$this->assertInstanceOf('PSX\Http\Exception\ClientErrorException', $e);
		$this->assertGreaterThanOrEqual(400, $e->getStatusCode());
		$this->assertLessThan(500, $e->getStatusCode());
		$this->assertEquals($statusCode, $e->getStatusCode());

		if($statusCode == 401)
		{
			$this->assertEquals('Basic', $e->getType());
			$this->assertEquals(array('realm' => 'psx'), $e->getParameters());
		}
		else if($statusCode == 405)
		{
			$this->assertEquals(array('GET', 'POST'), $e->getAllowedMethods());
		}
	}

	protected function assertServerError($statusCode, $className, array $arguments = array())
	{
		$e = $this->getException($className, $arguments);

		$this->assertInstanceOf('PSX\Http\Exception\ServerErrorException', $e);
		$this->assertGreaterThanOrEqual(500, $e->getStatusCode());
		$this->assertLessThan(600, $e->getStatusCode());
		$this->assertEquals($statusCode, $e->getStatusCode());
	}

	protected function getException($className, array $arguments = array())
	{
		$class     = new \ReflectionClass($className);
		$exception = $class->newInstanceArgs($arguments);

		try
		{
			throw $exception;
		}
		catch(\Exception $e)
		{
			return $e;
		}
	}
}

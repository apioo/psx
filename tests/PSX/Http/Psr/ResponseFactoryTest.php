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

namespace PSX\Http\Psr;

use Phly\Http\Response as PsrResponse;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\StringStream;

/**
 * ResponseFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testToPsr()
	{
		$response = new Response(
			200, 
			array('Content-Type' => 'text/plain'), 
			new StringStream('foobar')
		);

		$psrResponse = ResponseFactory::toPsr($response);

		$this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $psrResponse);
		$this->assertEquals(200, $psrResponse->getStatusCode());
		$this->assertEquals('text/plain', $psrResponse->getHeader('Content-Type'));
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $psrResponse->getBody());
		$this->assertEquals('foobar', (string) $psrResponse->getBody());
	}

	public function testFromPsr()
	{
		$psrResponse = new PsrResponse(
			new StringStream('foobar'),
			200,
			array('Content-Type' => 'text/plain')
		);

		$response = ResponseFactory::fromPsr($psrResponse);

		$this->assertInstanceOf('PSX\Http\ResponseInterface', $response);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('text/plain', $response->getHeader('Content-Type'));
		$this->assertInstanceOf('Psr\Http\Message\StreamableInterface', $response->getBody());
		$this->assertEquals('foobar', (string) $response->getBody());
	}
}

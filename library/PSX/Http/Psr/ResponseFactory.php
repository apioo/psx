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
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;

/**
 * ResponseFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseFactory
{
	/**
	 * Converts an PSX response into an PSR response
	 *
	 * @param PSX\Http\ResponseInterface
	 * @return Psr\Http\Message\ResponseInterface
	 */
	public static function toPsr(ResponseInterface $response)
	{
		return new PsrResponse(
			$response->getBody(),
			$response->getStatusCode(),
			$response->getHeaders()
		);
	}

	/**
	 * Converts an PSR response into an PSX response
	 *
	 * @param Psr\Http\Message\ResponseInterface
	 * @return PSX\Http\ResponseInterface
	 */
	public static function fromPsr(PsrResponseInterface $psrResponse)
	{
		return new Response(
			$psrResponse->getStatusCode(),
			$psrResponse->getHeaders(),
			$psrResponse->getBody()
		);
	}
}

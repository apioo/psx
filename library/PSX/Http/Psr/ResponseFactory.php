<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

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

use Phly\Http\Request as PsrRequest;
use Phly\Http\Uri as PsrUri;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Uri;

/**
 * RequestFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestFactory
{
	/**
	 * Converts an PSX request into an PSR request
	 *
	 * @param PSX\Http\RequestInterface
	 * @return Psr\Http\Message\RequestInterface
	 */
	public static function toPsr(RequestInterface $request)
	{
		return new PsrRequest(
			new PsrUri($request->getUri()->toString()),
			$request->getMethod(),
			$request->getBody(),
			$request->getHeaders()
		);
	}

	/**
	 * Converts an PSR request into an PSX request
	 *
	 * @param Psr\Http\Message\RequestInterface
	 * @return PSX\Http\RequestInterface
	 */
	public static function fromPsr(PsrRequestInterface $psrRequest)
	{
		return new Request(
			new Uri($psrRequest->getUri()),
			$psrRequest->getMethod(),
			$psrRequest->getHeaders(),
			$psrRequest->getBody()
		);
	}
}

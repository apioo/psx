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

use Phly\Http\ServerRequest as PsrServerRequest;
use Phly\Http\Uri as PsrUri;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Uri;

/**
 * ServerRequestFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServerRequestFactory
{
	/**
	 * Converts an PSX server request into an PSR server request
	 *
	 * @param PSX\Http\RequestInterface
	 * @return Psr\Http\Message\ServerRequestInterface
	 */
	public static function toPsr(RequestInterface $request)
	{
		$psrRequest = new PsrServerRequest(
			$request->getServerParams(), 
			$request->getFileParams(), 
			new PsrUri($request->getUri()->toString()), 
			$request->getMethod(), 
			$request->getBody(), 
			$request->getHeaders()
		);

		return $psrRequest
			->withCookieParams($request->getCookieParams())
			->withQueryParams($request->getQueryParams())
			->withBodyParams($request->getBodyParams());
	}

	/**
	 * Converts an PSR server request into an PSX server request
	 *
	 * @param Psr\Http\Message\ServerRequestInterface
	 * @return PSX\Http\RequestInterface
	 */
	public static function fromPsr(PsrServerRequestInterface $psrRequest)
	{
		$request = new Request(
			new Uri($psrRequest->getUri()),
			$psrRequest->getMethod(),
			$psrRequest->getHeaders(),
			$psrRequest->getBody()
		);

		$request->setBodyParams($psrRequest->getBodyParams());
		$request->setCookieParams($psrRequest->getCookieParams());
		$request->setFileParams($psrRequest->getFileParams());
		$request->setQueryParams($psrRequest->getQueryParams());
		$request->setServerParams($psrRequest->getServerParams());

		return $request;
	}
}

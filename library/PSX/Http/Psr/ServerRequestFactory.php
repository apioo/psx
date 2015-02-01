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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

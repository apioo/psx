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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

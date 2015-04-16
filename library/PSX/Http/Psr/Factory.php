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

use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Uri;

/**
 * Factory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Factory implements FactoryInterface
{
	public function getPsrRequest(RequestInterface $request)
	{
		// @TODO wait until the PSR gets accepted and use an open-source 
		// implementation

		return null;
	}

	public function getPsrServerRequest(RequestInterface $request)
	{
		// @TODO wait until the PSR gets accepted and use an open-source 
		// implementation

		return null;
	}

	public function getPsrResponse(ResponseInterface $response)
	{
		// @TODO wait until the PSR gets accepted and use an open-source 
		// implementation

		return null;
	}

	public function getNativeRequest(PsrRequestInterface $psrRequest)
	{
		return new Request(
			new Uri($psrRequest->getUri()),
			$psrRequest->getMethod(),
			$psrRequest->getHeaders(),
			$psrRequest->getBody()
		);
	}

	public function getNativeServerRequest(PsrServerRequestInterface $psrRequest)
	{
		$request = new Request(
			new Uri($psrRequest->getUri()),
			$psrRequest->getMethod(),
			$psrRequest->getHeaders(),
			$psrRequest->getBody()
		);

		return $request;
	}

	public function getNativeResponse(PsrResponseInterface $psrResponse)
	{
		return new Response(
			$psrResponse->getStatusCode(),
			$psrResponse->getHeaders(),
			$psrResponse->getBody()
		);
	}
}

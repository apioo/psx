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
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * FactoryInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface FactoryInterface
{
	/**
	 * Converts an PSX request into an PSR request
	 *
	 * @param PSX\Http\RequestInterface
	 * @return Psr\Http\Message\RequestInterface
	 */
	public function getPsrRequest(RequestInterface $request);

	/**
	 * Converts an PSX request into an PSR server request
	 *
	 * @param PSX\Http\RequestInterface
	 * @return Psr\Http\Message\ServerRequestInterface
	 */
	public function getPsrServerRequest(RequestInterface $request);

	/**
	 * Converts an PSX response into an PSR response
	 *
	 * @param PSX\Http\ResponseInterface
	 * @return Psr\Http\Message\ResponseInterface
	 */
	public function getPsrResponse(ResponseInterface $response);

	/**
	 * Converts an PSR request into an PSX request
	 *
	 * @param Psr\Http\Message\RequestInterface
	 * @return PSX\Http\RequestInterface
	 */
	public function getNativeRequest(PsrRequestInterface $psrRequest);

	/**
	 * Converts an PSR server request into an PSX server request
	 *
	 * @param Psr\Http\Message\ServerRequestInterface
	 * @return PSX\Http\RequestInterface
	 */
	public function getNativeServerRequest(PsrServerRequestInterface $psrRequest);

	/**
	 * Converts an PSR response into an PSX response
	 *
	 * @param Psr\Http\Message\ResponseInterface
	 * @return PSX\Http\ResponseInterface
	 */
	public function getNativeResponse(PsrResponseInterface $psrResponse);
}


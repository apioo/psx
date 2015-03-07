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

namespace PSX\Http;

/**
 * This is a mutable version of the PSR HTTP message interface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md
 */
interface ResponseInterface extends MessageInterface
{
	/**
	 * Gets the response Status-Code
	 *
	 * @return integer
	 */
	public function getStatusCode();

	/**
	 * Gets the response Reason-Phrase, a short textual description of the 
	 * Status-Code
	 *
	 * @return string
	 */
	public function getReasonPhrase();

	/**
	 * Sets the status code and reason phrase. If no reason phrase is provided
	 * the standard message according to the status code is used. If the status
	 * code is unknown an reason phrase must be provided 
	 *
	 * @param integer $code
	 * @param integer $reasonPhrase
	 */
	public function setStatus($code, $reasonPhrase = null);
}

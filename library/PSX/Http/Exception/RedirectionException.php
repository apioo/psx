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

namespace PSX\Http\Exception;

/**
 * This class of status code indicates that further action needs to be taken by 
 * the user agent in order to fulfill the request. The action required MAY be 
 * carried out by the user agent without interaction with the user if and only 
 * if the method used in the second request is GET or HEAD. A client SHOULD 
 * detect infinite redirection loops, since such loops generate network traffic 
 * for each redirection.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RedirectionException extends StatusCodeException
{
	protected $location;

	public function __construct($statusCode, $location = null)
	{
		parent::__construct('Redirect exception', $statusCode);

		$this->location = $location;
	}

	public function getLocation()
	{
		return $this->location;
	}
}

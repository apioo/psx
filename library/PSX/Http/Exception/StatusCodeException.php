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

use InvalidArgumentException;
use PSX\Exception;
use PSX\Http;

/**
 * StatusCodeException
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StatusCodeException extends Exception
{
	protected $statusCode;

	public function __construct($message, $statusCode)
	{
		parent::__construct($message);

		if(isset(Http::$codes[$statusCode]))
		{
			$this->statusCode = $statusCode;
		}
		else
		{
			throw new InvalidArgumentException('Invalid http status code');
		}
	}

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function isInformational()
	{
		return $this->statusCode >= 100 && $this->statusCode < 200;
	}

	public function isSuccessful()
	{
		return $this->statusCode >= 200 && $this->statusCode < 300;
	}

	public function isRedirection()
	{
		return $this->statusCode >= 300 && $this->statusCode < 400;
	}

	public function isClientError()
	{
		return $this->statusCode >= 400 && $this->statusCode < 500;
	}

	public function isServerError()
	{
		return $this->statusCode >= 500 && $this->statusCode < 600;
	}
}

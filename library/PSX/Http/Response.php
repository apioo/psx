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

namespace PSX\Http;

use PSX\Http;
use PSX\Exception;

/**
 * Response
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Response extends Message implements ResponseInterface
{
	protected $code;
	protected $reasonPhrase;

	/**
	 * @param integer $code
	 * @param array $headers
	 * @param string $body
	 */
	public function __construct($code = null, array $headers = array(), $body = null)
	{
		parent::__construct($headers, $body);

		$this->code = $code;
	}

	/**
	 * Returns the http response code
	 *
	 * @return integer
	 */
	public function getStatusCode()
	{
		return $this->code;
	}

	/**
	 * Returns the http response message. That means the last part of the status
	 * line i.e. "OK" from an 200 response
	 *
	 * @return string
	 */
	public function getReasonPhrase()
	{
		return $this->reasonPhrase;
	}

	/**
	 * Sets the status code and reason phrase. If no reason phrase is provided
	 * the standard message according to the status code is used
	 *
	 * @param integer $code
	 * @param integer $reasonPhrase
	 */
	public function setStatus($code, $reasonPhrase = null)
	{
		$this->code = (int) $code;

		if($reasonPhrase !== null)
		{
			$this->reasonPhrase = $reasonPhrase;
		}
		else if(isset(Http::$codes[$this->code]))
		{
			$this->reasonPhrase = Http::$codes[$this->code];
		}
	}

	/**
	 * Converts the response object to an http response string
	 *
	 * @return string
	 */
	public function toString()
	{
		$response = $this->getLine() . Http::$newLine;
		$headers  = ResponseParser::buildHeaderFromMessage($this);

		foreach($headers as $header)
		{
			$response.= $header . Http::$newLine;
		}

		$response.= Http::$newLine;
		$response.= (string) $this->getBody();

		return $response;
	}

	/**
	 * Returns the http response line
	 *
	 * @return string
	 */
	public function getLine()
	{
		$protocol = $this->getProtocolVersion();
		$code     = $this->getStatusCode();
		$phrase   = $this->getReasonPhrase();

		if(empty($code))
		{
			throw new Exception('Status code not set');
		}

		$protocol = !empty($protocol) ? $protocol : 'HTTP/1.1';

		if(empty($phrase) && isset(Http::$codes[$code]))
		{
			$phrase = Http::$codes[$code];
		}

		if(empty($phrase))
		{
			throw new Exception('No reason phrase provided');
		}

		return $protocol . ' ' . $code . ' ' . $phrase;
	}

	public function __toString()
	{
		return $this->toString();
	}
}

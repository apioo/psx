<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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
class Response extends Message
{
	private $scheme;
	private $code;
	private $message;

	/**
	 * __construct
	 *
	 * @param string $scheme
	 * @param integer $code
	 * @param string $message
	 * @param array $header
	 * @param string $body
	 */
	public function __construct($scheme = null, $code = null, $message = null, array $header = array(), $body = null)
	{
		parent::__construct($header, $body);

		$this->setScheme($scheme);
		$this->setCode($code);
		$this->setMessage($message);
	}

	/**
	 * Sets the http scheme probably HTTP/1.0 or HTTP/1.1
	 *
	 * @param string $scheme
	 * @return void
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}

	/**
	 * Sets the response code
	 *
	 * @param integer $code
	 * @return void
	 */
	public function setCode($code)
	{
		$this->code = (integer) $code;
	}

	/**
	 * Sets the response message
	 *
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * Returns the http scheme
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Returns the http response code
	 *
	 * @return integer
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Returns the http response message. That means the last part of the status
	 * line i.e. "OK" from an 200 response
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Converts an raw http response into an PSX\Http\Response object. Throws an
	 * exception if the response has not an valid status line
	 *
	 * @param string $content
	 * @return PSX\Http\Response
	 */
	public static function convert($content)
	{
		if(empty($content))
		{
			throw new Exception('Empty response');
		}

		$response = new self();

		list($scheme, $code, $message) = self::getStatus($content);

		$response->setScheme($scheme);
		$response->setCode($code);
		$response->setMessage($message);

		list($header, $body) = self::splitResponse($content);

		$response->setHeader(self::headerToArray($header));
		$response->setBody($body);

		return $response;
	}

	/**
	 * Splits an given http response into the string and header part by
	 * searching for the first occurence of CRLF CRLF
	 *
	 * @param string $response
	 * @return array
	 */
	public static function splitResponse($response)
	{
		$pos    = strpos($response, Http::$newLine . Http::$newLine);
		$header = substr($response, 0, $pos);
		$body   = trim(substr($response, $pos + 1));

		return array($header, $body);
	}

	/**
	 * Parses an raw http header string into an array. The key is transformed to
	 * lowercase (the RFC states that the header fields are case-insensitive)
	 * because php arrays are case sensitive. So you can access the key always
	 * as lowercase i.e.
	 * <code>
	 * $contentType = isset($header['content-type']) ? $header['content-type'] : null;
	 * </code>
	 *
	 * @param string $header
	 * @return array<string, string>
	 */
	public static function headerToArray($header)
	{
		$lines  = explode(Http::$newLine, $header);
		$header = array();

		foreach($lines as $line)
		{
			$parts = explode(':', $line, 2);

			if(isset($parts[0]) && isset($parts[1]))
			{
				$key   = strtolower(trim($parts[0]));
				$value = trim($parts[1]);

				if($key == 'set-cookie')
				{
					if(!isset($header[$key]))
					{
						$header[$key] = array();
					}

					$header[$key][] = $value;
				}
				else
				{
					$header[$key] = $value;
				}
			}
		}

		return $header;
	}

	private static function getStatus($response)
	{
		$line = self::getStatusLine($response);

		if($line !== false)
		{
			$parts = explode(' ', $line, 3);

			if(isset($parts[0]) && isset($parts[1]) && isset($parts[2]))
			{
				$scheme  = strval($parts[0]);
				$code    = intval($parts[1]);
				$message = strval($parts[2]);

				return array($scheme, $code, $message);
			}
			else
			{
				throw new ParseException('Invalid status line format');
			}
		}
		else
		{
			throw new ParseException('Couldnt find status line');
		}
	}

	private static function getStatusLine($response)
	{
		$pos = strpos($response, Http::$newLine);

		if($pos !== false)
		{
			return substr($response, 0, $pos);
		}
		else
		{
			return false;
		}
	}
}


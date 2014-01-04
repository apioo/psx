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
	 * Returns the http request line
	 *
	 * @return string
	 */
	public function getLine()
	{
		return $this->getScheme() . ' ' . $this->getCode() . ' ' . $this->getMessage();
	}

	/**
	 * Tries to detect the character encoding of the body. Returns the detected 
	 * charset or false
	 *
	 * @return string|false
	 */
	public function getCharset()
	{
		// header
		$contentType = $this->getHeader('Content-Type');

		if(!empty($contentType))
		{
			$pos = strpos($contentType, 'charset=');

			if($pos !== false)
			{
				return strtoupper(trim(substr($contentType, $pos + 8)));
			}
		}

		// meta
		// @todo check for meta http-equiv content-type tag

		// detect
		// @todo try to guess encoding with mb_detect_encoding

		return false;
	}

	/**
	 * Converts the body to the given outCharset if the encoding of the body 
	 * could be detected
	 *
	 * @param string $outCharset
	 * @return string
	 */
	public function getBodyAsString($outCharset = 'UTF-8//IGNORE')
	{
		$inCharset = $this->getCharset();
		$body      = $this->getBody();

		if($inCharset !== false)
		{
			$body = iconv($inCharset, $outCharset, $body);
		}

		return $body;
	}

	/**
	 * Converts the response object to an http response string
	 *
	 * @return string
	 */
	public function toString()
	{
		$response = $this->getLine() . Http::$newLine;

		foreach($this->header as $k => $v)
		{
			$response.= $k . ': ' . $v . Http::$newLine;
		}

		$response.= Http::$newLine;
		$response.= $this->getBody();

		return $response;
	}

	/**
	 * Converts an raw http response into an PSX\Http\Response object. Throws an
	 * exception if the response has not an valid status line
	 *
	 * @param string $content
	 * @return PSX\Http\Response
	 */
	public static function convert($content, $mode = ResponseParser::MODE_STRICT)
	{
		$parser = new ResponseParser($mode);

		return $parser->parse($content);
	}
}


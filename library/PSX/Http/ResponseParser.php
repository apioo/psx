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

use RuntimeException;
use PSX\Http;
use PSX\Http\Stream\StringStream;
use PSX\Exception;

/**
 * ResponseParser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseParser
{
	const MODE_STRICT = 0x1;
	const MODE_LOOSE  = 0x2;

	protected $mode;

	/**
	 * The mode indicates how the header is detected in strict mode we search 
	 * exactly for CRLF CRLF in loose mode we look for the first empty line. In
	 * loose mode we can parse an header wich was defined in the code means is
	 * not strictly seperated by CRLF
	 *
	 * @param integer $mode
	 */
	public function __construct($mode = self::MODE_STRICT)
	{
		$this->mode = $mode;
	}

	/**
	 * Converts an raw http response into an PSX\Http\Response object
	 *
	 * @param string $content
	 * @return PSX\Http\Response
	 */
	public function parse($content)
	{
		if(empty($content))
		{
			throw new Exception('Empty response');
		}

		if($this->mode == self::MODE_LOOSE)
		{
			$content = str_replace(array("\r\n", "\n", "\r"), "\n", $content);
		}

		$response = new Response();

		list($scheme, $code, $message) = $this->getStatus($content);

		$response->setProtocolVersion($scheme);
		$response->setStatusCode($code);
		$response->setReasonPhrase($message);

		list($header, $body) = $this->splitResponse($content);

		$this->headerToArray($response, $header);

		$response->setBody(new StringStream($body));

		return $response;
	}

	protected function getStatus($response)
	{
		$line = $this->getStatusLine($response);

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

	/**
	 * Splits an given http response into the string and header part
	 *
	 * @param string $response
	 * @return array
	 */
	protected function splitResponse($response)
	{
		if($this->mode == self::MODE_STRICT)
		{
			$pos    = strpos($response, Http::$newLine . Http::$newLine);
			$header = substr($response, 0, $pos);
			$body   = trim(substr($response, $pos + 1));
		}
		else if($this->mode == self::MODE_LOOSE)
		{
			$lines  = explode("\n", $response);
			$header = '';
			$body   = '';
			$found  = false;
			$count  = count($lines);

			foreach($lines as $i => $line)
			{
				$line = trim($line);

				if(!$found && empty($line))
				{
					$found = true;
					continue;
				}

				if(!$found)
				{
					$header.= $line . Http::$newLine;
				}
				else
				{
					$body.= $line . ($i < $count - 1 ? "\n" : '');
				}
			}
		}
		else
		{
			throw new RuntimeException('Invalid parse mode');
		}

		return array($header, $body);
	}

	/**
	 * Parses an raw http header string into an array. The key is transformed to
	 * lowercase (the RFC states that the header fields are case-insensitive)
	 * because php arrays are case sensitive
	 *
	 * @param string $header
	 * @return array<string, string>
	 */
	protected function headerToArray(Response $response, $header)
	{
		$lines  = explode(Http::$newLine, $header);
		$header = array();

		foreach($lines as $line)
		{
			$parts = explode(':', $line, 2);

			if(isset($parts[0]) && isset($parts[1]))
			{
				$key   = $parts[0];
				$value = substr($parts[1], 1);

				$response->addHeader($key, $value);
			}
		}
	}

	protected function getStatusLine($response)
	{
		if($this->mode == self::MODE_STRICT)
		{
			$pos = strpos($response, Http::$newLine);
		}
		else if($this->mode == self::MODE_LOOSE)
		{
			$pos = strpos($response, "\n");
		}

		return $pos !== false ? substr($response, 0, $pos) : false;
	}

	public static function buildResponseFromHeader(array $headers)
	{
		$line = current($headers);

		if(!empty($line))
		{
			$parts = explode(' ', trim($line), 3);

			if(isset($parts[0]) && isset($parts[1]) && isset($parts[2]))
			{
				$scheme  = strval($parts[0]);
				$code    = intval($parts[1]);
				$message = strval($parts[2]);

				$response = new Response($scheme, $code, $message);

				// append header
				foreach($headers as $line)
				{
					$parts = explode(':', $line, 2);

					if(isset($parts[0]) && isset($parts[1]))
					{
						$key   = $parts[0];
						$value = trim($parts[1]);

						$response->addHeader($key, $value);
					}
				}

				return $response;
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

	public static function buildHeaderFromResponse(Response $response)
	{
		$headers = $response->getHeaders();
		$result  = array();

		foreach($headers as $key => $value)
		{
			if($key == 'set-cookie')
			{
				foreach($value as $cookie)
				{
					$result[] = $key . ': ' . $cookie;
				}
			}
			else
			{
				$result[] = $key . ': ' . $value;
			}
		}

		return $result;
	}
}

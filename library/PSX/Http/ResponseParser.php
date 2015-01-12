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
class ResponseParser extends ParserAbstract
{
	/**
	 * Converts an raw http response into an PSX\Http\Response object
	 *
	 * @param string $content
	 * @return PSX\Http\Response
	 */
	public function parse($content)
	{
		$content = $this->normalize($content);

		list($scheme, $code, $message) = $this->getStatus($content);

		$response = new Response();
		$response->setProtocolVersion($scheme);
		$response->setStatusCode($code);
		$response->setReasonPhrase($message);

		list($header, $body) = $this->splitMessage($content);

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
				$scheme  = $parts[0];
				$code    = intval($parts[1]);
				$message = $parts[2];

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

	public static function buildResponseFromHeader(array $headers)
	{
		$line = array_shift($headers);

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
}

<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Exception;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * RequestParser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestParser extends ParserAbstract
{
	protected $baseUrl;

	public function __construct($baseUrl, $mode = self::MODE_STRICT)
	{
		parent::__construct($mode);

		$this->baseUrl = $baseUrl;
	}

	/**
	 * Converts an raw http request into an PSX\Http\Request object
	 *
	 * @param string $content
	 * @return PSX\Http\Request
	 */
	public function parse($content)
	{
		$content = $this->normalize($content);

		list($method, $path, $scheme) = $this->getStatus($content);

		$request = new Request(new Url($this->baseUrl . '/' . ltrim($path, '/')), $method);
		$request->setProtocolVersion($scheme);

		list($header, $body) = $this->splitMessage($content);

		$this->headerToArray($request, $header);

		$request->setBody(new StringStream($body));

		return $request;
	}

	protected function getStatus($request)
	{
		$line = $this->getStatusLine($request);

		if($line !== false)
		{
			$parts = explode(' ', $line, 3);

			if(isset($parts[0]) && isset($parts[1]) && isset($parts[2]))
			{
				$method = $parts[0];
				$path   = $parts[1];
				$scheme = $parts[2];

				return array($method, $path, $scheme);
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

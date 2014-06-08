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

namespace PSX\Dispatch;

use PSX\Http;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\FileStream;

/**
 * Sender
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sender implements SenderInterface
{
	protected $chunkSize = 8192;

	public function setChunkSize($chunkSize)
	{
		$this->chunkSize = $chunkSize;
	}

	public function send(Response $response)
	{
		if(!$this->isCli())
		{
			// content and transfer encoding is only useful if we are not in an
			// cli environment
			$transferEncoding = $response->getHeader('Transfer-Encoding');
			$contentEncoding  = $response->getHeader('Content-Encoding');

			// send response code
			$scheme = $response->getProtocolVersion();
			if(empty($scheme))
			{
				$scheme = 'HTTP/1.1';
			}

			$code = $response->getCode();
			if(!isset(Http::$codes[$code]))
			{
				$code = 200;
			}

			$this->sendHeader($scheme . ' ' . $code . ' ' . Http::$codes[$code]);

			// if we have an locaion header
			$location = $response->getHeader('Location');

			if(!empty($location))
			{
				$this->sendHeader('Location: ' . $location);
				return;
			}

			// send header
			$headers = ResponseParser::buildHeaderFromMessage($response);

			foreach($headers as $header)
			{
				$this->sendHeader($header);
			}
		}
		else
		{
			$transferEncoding = null;
			$contentEncoding  = null;
		}

		// send body
		if($transferEncoding == 'chunked')
		{
			$this->sendContentChunked($response);
		}
		else
		{
			$this->sendContentEncoded($contentEncoding, $response);
		}
	}

	protected function sendHeader($header)
	{
		header($header);
	}

	protected function sendContentEncoded($contentEncoding, Response $response)
	{
		switch($contentEncoding)
		{
			case 'deflate':
				$body = (string) $response->getBody();

				echo gzcompress($body);
				break;

			case 'gzip':
			case 'x-gzip':
				$body = (string) $response->getBody();

				echo gzencode($body);
				break;

			default:
				echo (string) $response->getBody();
				break;
		}
	}

	protected function sendContentChunked(Response $response)
	{
		$body = $response->getBody();
		$body->seek(0);

		while(!$body->eof())
		{
			$chunk = $body->read($this->chunkSize);

			echo dechex(strlen($chunk)) . "\r\n" . $chunk . "\r\n";
			flush();
		}

		echo '0' . "\r\n" . "\r\n";
		flush();

		$body->close();
	}

	protected function isCli()
	{
		return PHP_SAPI == 'cli';
	}
}

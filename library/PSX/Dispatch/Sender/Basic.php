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

namespace PSX\Dispatch\Sender;

use Psr\Http\Message\ResponseInterface;
use PSX\Dispatch\SenderInterface;
use PSX\Http;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\FileStream;

/**
 * Basic sender which handles file stream bodies, content encoding and transfer
 * encoding
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Basic implements SenderInterface
{
	protected $chunkSize = 8192;

	/**
	 * The chunk size which is used if the transfer encoding is "chunked"
	 *
	 * @param integer $chunkSize
	 */
	public function setChunkSize($chunkSize)
	{
		$this->chunkSize = $chunkSize;
	}

	public function send(ResponseInterface $response)
	{
		// remove body on specific status codes
		if(in_array($response->getStatusCode(), array(100, 101, 204, 304)))
		{
			$response->setBody(null);
		}
		// if we have an location header we dont send any content
		else if($response->hasHeader('Location'))
		{
			$response->setBody(null);
		}

		if(!$this->isCli())
		{
			// if we have an file stream body set custom header
			$this->prepareFileStream($response);

			// send status line
			$this->sendStatusLine($response);

			// send headers
			$this->sendHeaders($response);
		}

		// send body
		$this->sendBody($response);
	}

	protected function isCli()
	{
		return PHP_SAPI == 'cli';
	}

	protected function prepareFileStream(ResponseInterface $response)
	{
		if($response->getBody() instanceof FileStream)
		{
			$fileName = $response->getBody()->getFileName();
			if(empty($fileName))
			{
				$fileName = 'file';
			}

			$contentType = $response->getBody()->getContentType();
			if(empty($contentType))
			{
				$contentType = 'application/octet-stream';
			}

			$response->setHeader('Content-Type', $contentType);
			$response->setHeader('Content-Disposition', 'attachment; filename="' . addcslashes($fileName, '"') . '"');
			$response->setHeader('Transfer-Encoding', 'chunked');
		}
	}

	protected function sendStatusLine(ResponseInterface $response)
	{
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
	}

	protected function sendHeaders(ResponseInterface $response)
	{
		$headers = ResponseParser::buildHeaderFromMessage($response);

		foreach($headers as $header)
		{
			$this->sendHeader($header);
		}
	}

	protected function sendHeader($header)
	{
		header($header);
	}

	protected function sendBody(ResponseInterface $response)
	{
		if($response->getBody() !== null)
		{
			$transferEncoding = $response->getHeader('Transfer-Encoding');
			$contentEncoding  = $response->getHeader('Content-Encoding');

			if($transferEncoding == 'chunked')
			{
				$this->sendContentChunked($response);
			}
			else
			{
				$this->sendContentEncoded($contentEncoding, $response);
			}
		}
	}

	protected function sendContentEncoded($contentEncoding, ResponseInterface $response)
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

	protected function sendContentChunked(ResponseInterface $response)
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
}

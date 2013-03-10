<?php
/*
 *  $Id: Socks.php 579 2012-08-14 18:22:10Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http\Handler;

use PSX\Http;
use PSX\Http\HandlerException;
use PSX\Http\HandlerInterface;
use PSX\Http\NotSupportedException;
use PSX\Http\Request;

/**
 * PSX_Http_Handler_Socks
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Http
 * @version    $Revision: 579 $
 */
class Socks implements HandlerInterface
{
	private $lastError;
	private $request;
	private $response;

	public function request(Request $request, $count = 0)
	{
		$scheme = null;

		if(!$request->isSSL())
		{
			$scheme = 'tcp';
		}
		else
		{
			$transports = stream_get_transports();

			if(in_array('tls', $transports))
			{
				$scheme = 'tls';
			}
			else if(in_array('ssl', $transports))
			{
				$scheme = 'ssl';
			}
			else
			{
				throw new NotSupportedException('https is not supported by the system');
			}
		}


		$port = $request->getUrl()->getPort();

		if(empty($port))
		{
			$port = getservbyname($request->getUrl()->getScheme(), 'tcp');
		}


		$handle = fsockopen($scheme . '://' . $request->getUrl()->getHost(), $port, $errno, $errstr);

		if($handle !== false)
		{
			$timeout = $request->getTimeout();

			if(!empty($timeout))
			{
				stream_set_timeout($handle, $timeout);
			}


			$callback = $request->getCallback();

			if(!empty($callback))
			{
				call_user_func_array($callback, array($handle, $request));
			}


			// writer request
			if(!fwrite($handle, $request->toString()))
			{
				throw new HandlerException('Couldnt write to stream');
			}

			fflush($handle);


			// read response
			$response = '';


			// read header
			$headers = array();

			do
			{
				$header = trim(fgets($handle));
				$pos    = strpos($header, ':');

				if($pos !== false)
				{
					$key   = substr($header, 0, $pos);
					$value = substr($header, $pos + 2);

					$headers[strtolower($key)] = $value;
				}

				$response.= $header . Http::$newLine;
			}
			while(!empty($header));

			$response.= Http::$newLine;


			// read body
			$contentLength    = isset($headers['content-length']) ? (integer) $headers['content-length'] : 0;
			$transferEncoding = isset($headers['transfer-encoding']) ? $headers['transfer-encoding'] : null;
			$body             = '';

			// content-length
			if($contentLength > 0)
			{
				$body = $this->readContent($handle, $contentLength);
			}

			// transfer encoding chunked
			if($transferEncoding == 'chunked')
			{
				do
				{
					$size = hexdec(trim(fgets($handle)));
					$body.= $this->readContent($handle, $size);

					fgets($handle);
				}
				while($size > 0);
			}

			$response.= $body;


			// close stream
			fclose($handle);


			$this->lastError = false;
			$this->request   = $request;
			$this->response  = $response;

			return $response;
		}
		else
		{
			$this->lastError = $errstr;
			$this->request   = false;
			$this->response  = false;

			return false;
		}
	}

	public function getLastError()
	{
		return $this->lastError;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getResponse()
	{
		return $this->response;
	}

	private function readContent($handle, $size)
	{
		$content = '';
		$read    = 0;
		$buffer  = $size;

		do
		{
			$content.= stream_get_contents($handle, $buffer);
			$read   += strlen($content);
			$buffer  = $buffer - $read;
		}
		while($read < $size);

		return $content;
	}
}


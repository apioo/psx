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

namespace PSX\Http\Handler;

use InvalidArgumentException;
use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\HandlerException;
use PSX\Http\Options;
use PSX\Http\RedirectException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\StringStream;
use PSX\Http\Stream\TempStream;

/**
 * This handler uses the curl extension to send the HTTP request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Curl implements HandlerInterface
{
	protected $header;
	protected $body;

	protected $hasFollowLocation = false;

	public function setFollowLocation($followLocation)
	{
		$this->hasFollowLocation = (bool) $followLocation;
	}

	public function request(Request $request, Options $options)
	{
		$this->header = array();
		$this->body   = fopen('php://temp', 'r+');

		$handle = curl_init($request->getUri()->toString());

		curl_setopt($handle, CURLOPT_HEADER, false);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($handle, CURLOPT_HEADERFUNCTION, array($this, 'header'));
		curl_setopt($handle, CURLOPT_WRITEFUNCTION, array($this, 'write'));
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $request->getMethod());

		// set header
		$headers = ResponseParser::buildHeaderFromMessage($request);

		if(!empty($headers))
		{
			if(!$request->hasHeader('Expect'))
			{
				$headers[] = 'Expect:';
			}

			curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		}

		// set body
		$body = $request->getBody();

		if($body !== null && !in_array($request->getMethod(), array('HEAD', 'GET')))
		{
			if($request->getHeader('Transfer-Encoding') == 'chunked')
			{
				curl_setopt($handle, CURLOPT_UPLOAD, true);
				curl_setopt($handle, CURLOPT_READFUNCTION, function($handle, $fd, $length) use ($body) {
					return $body->read($length);
				});
			}
			else
			{
				curl_setopt($handle, CURLOPT_POSTFIELDS, (string) $body);
			}
		}

		// set proxy
		$proxy = $options->getProxy();

		if(!empty($proxy))
		{
			curl_setopt($handle, CURLOPT_PROXY, $proxy);
		}

		// set follow location
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, $options->getFollowLocation() && $this->hasFollowLocation);
		curl_setopt($handle, CURLOPT_MAXREDIRS, $options->getMaxRedirects());

		// set ssl
		if($options->getSsl() !== false && ($options->getSsl() === true || strcasecmp($request->getUri()->getScheme(), 'https') === 0))
		{
			$caPath = $options->getCaPath();

			if(!empty($caPath))
			{
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);

				if(is_file($caPath))
				{
					curl_setopt($handle, CURLOPT_CAINFO, $caPath);
				}
				else if(is_dir($caPath))
				{
					curl_setopt($handle, CURLOPT_CAPATH, $caPath);
				}
			}
			else
			{
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
			}
		}

		// set timeout
		$timeout = $options->getTimeout();

		if(!empty($timeout))
		{
			curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
		}

		// callback
		$callback = $options->getCallback();

		if(!empty($callback))
		{
			call_user_func_array($callback, array($handle, $request));
		}


		curl_exec($handle);


		// if follow location is active modify the header since all headers from
		// each redirection are included
		if($options->getFollowLocation() && $this->hasFollowLocation)
		{
			$positions = array();
			foreach($this->header as $key => $header)
			{
				if(substr($header, 0, 5) == 'HTTP/')
				{
					$positions[] = $key;
				}
			}

			if(count($positions) > 1)
			{
				$this->header = array_slice($this->header, end($positions) - 1);
			}
		}


		if(curl_errno($handle))
		{
			throw new HandlerException('Curl error: ' . curl_error($handle));
		}

		curl_close($handle);

		// build response
		rewind($this->body);

		$response = ResponseParser::buildResponseFromHeader($this->header);

		if($request->getMethod() != 'HEAD')
		{
			$response->setBody(new TempStream($this->body));
		}
		else
		{
			$response->setBody(new StringStream());
		}

		return $response;
	}

	protected function header($curl, $data)
	{
		$this->header[] = $data;

		return strlen($data);
	}

	protected function write($curl, $data)
	{
		return fwrite($this->body, $data);
	}
}

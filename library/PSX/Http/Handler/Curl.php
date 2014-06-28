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

namespace PSX\Http\Handler;

use InvalidArgumentException;
use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\HandlerException;
use PSX\Http\RedirectException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\TempStream;

/**
 * The logic of following redirects is handeled in the PSX\Http class to avoid
 * that handlers have to deal with this. But because curl has an follow location
 * option wich is probably faster you can enable this in the handler. Note
 * CURLOPT_FOLLOWLOCATION throws an error if in safe mode or open_basedir is
 * set. If you use the curl redirection the redirect will not include any
 * cookies of the store. Because of these drawbacks the option is by default
 * disabled
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Curl implements HandlerInterface
{
	protected $hasFollowLocation;
	protected $caInfo;
	protected $proxy;

	protected $header;
	protected $body;

	public function __construct()
	{
		$this->hasFollowLocation = false;
	}

	public function setFollowLocation($followLocation)
	{
		$this->hasFollowLocation = (bool) $followLocation;
	}

	public function setProxy($proxy)
	{
		$this->proxy = $proxy;
	}

	/**
	 * Sets the name of a file holding one or more certificates to verify the
	 * peer with
	 *
	 * @param string $path
	 * @return void
	 */
	public function setCaInfo($path)
	{
		$this->caInfo = $path;
	}

	public function request(Request $request, $count = 0)
	{
		$this->header = array();
		$this->body   = fopen('php://temp', 'r+');

		$handle = curl_init($request->getUrl()->__toString());

		curl_setopt($handle, CURLOPT_HEADER, false);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($handle, CURLOPT_HEADERFUNCTION, array($this, 'header'));
		curl_setopt($handle, CURLOPT_WRITEFUNCTION, array($this, 'write'));
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $request->getMethod());

		if(!empty($this->proxy))
		{
			curl_setopt($handle, CURLOPT_PROXY, $this->proxy);
		}

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

		// set follow location
		if($request->getFollowLocation() && $this->hasFollowLocation)
		{
			curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($handle, CURLOPT_MAXREDIRS, $request->getMaxRedirects());
		}

		// set ssl
		if($request->isSSL())
		{
			if(!empty($this->caInfo))
			{
				curl_setopt($handle, CURLOPT_CAINFO, $this->caInfo);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
			}
			else
			{
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
			}
		}

		// set timeout
		$timeout = $request->getTimeout();

		if(!empty($timeout))
		{
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
		}

		// callback
		$callback = $request->getCallback();

		if(!empty($callback))
		{
			call_user_func_array($callback, array($handle, $request));
		}


		curl_exec($handle);


		// if follow location is active modify the header since all headers from
		// each redirection are included
		if($request->getFollowLocation() && $this->hasFollowLocation)
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
			$response->setBody(null);
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

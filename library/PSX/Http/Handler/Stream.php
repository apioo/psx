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

use PSX\Http;
use PSX\Http\HandlerException;
use PSX\Http\HandlerInterface;
use PSX\Http\NotSupportedException;
use PSX\Http\Options;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\TempStream;
use PSX\Http\Stream\StringStream;

/**
 * Stream
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Stream implements HandlerInterface
{
	protected $hasFollowLocation = false;

	public function setFollowLocation($followLocation)
	{
		$this->hasFollowLocation = (bool) $followLocation;
	}

	public function request(Request $request, Options $options)
	{
		$sslOptions  = null;
		$httpOptions = array(
			'method' => $request->getMethod(),
			'protocol_version' => 1.1,
		);

		// until chunked transfer encoding if fully implemented we remove the
		// header
		if($request->hasHeader('Transfer-Encoding'))
		{
			$request->removeHeader('Transfer-Encoding');
		}

		// set header
		$headers = implode(Http::$newLine, ResponseParser::buildHeaderFromMessage($request));

		$httpOptions['header'] = $headers;

		// set body
		$body = $request->getBody();

		if($body !== null && !in_array($request->getMethod(), array('HEAD', 'GET')))
		{
			$httpOptions['content'] = (string) $body;
		}

		// set proxy
		$proxy = $options->getProxy();
		if(!empty($proxy))
		{
			$httpOptions['proxy'] = $proxy;
		}

		// set follow location
		if($options->getFollowLocation() && $this->hasFollowLocation)
		{
			$httpOptions['follow_location'] = 1;
			$httpOptions['max_redirects']   = $options->getMaxRedirects();
		}
		else
		{
			$httpOptions['follow_location'] = 0;
			$httpOptions['max_redirects']   = $options->getMaxRedirects();
		}

		// set ssl
		if($options->getSsl() !== false && ($options->getSsl() === true || strcasecmp($request->getUri()->getScheme(), 'https') === 0))
		{
			$caPath = $options->getCaPath();

			if(!empty($caPath))
			{
				$sslOptions['verify_peer']       = true;
				$sslOptions['allow_self_signed'] = true;

				if(is_file($caPath))
				{
					$sslOptions['cafile'] = $caPath;
				}
				else if(is_dir($caPath))
				{
					$sslOptions['capath'] = $caPath;
				}
			}
			else
			{
				$sslOptions['verify_peer']       = false;
				$sslOptions['allow_self_signed'] = false;
			}
		}

		// set timeout
		$timeout = $options->getTimeout();

		if(!empty($timeout))
		{
			$httpOptions['timeout'] = $timeout;
		}

		// create context
		$ctxOptions['http'] = $httpOptions;

		if(!empty($sslOptions))
		{
			$ctxOptions['ssl'] = $sslOptions;
		}

		$ctx = stream_context_create($ctxOptions);

		// callback
		$callback = $options->getCallback();

		if(!empty($callback))
		{
			call_user_func_array($callback, array($ctx, $request));
		}

		// open socket
		set_error_handler(__CLASS__ . '::handleError');
		$handle = fopen($request->getUri()->toString(), 'r', false, $ctx);
		restore_error_handler();

		// check for timeout
		$meta = stream_get_meta_data($handle);

		if($meta['timed_out'])
		{
			throw new HandlerException('Connection timeout');
		}

		// build response
		$response = ResponseParser::buildResponseFromHeader($http_response_header);

		if($request->getMethod() != 'HEAD')
		{
			$response->setBody(new TempStream($handle));
		}
		else
		{
			$response->setBody(new StringStream());
		}

		return $response;
	}

	public static function handleError($errno, $errstr)
	{
		throw new HandlerException($errstr, $errno);
	}
}


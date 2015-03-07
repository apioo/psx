<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * This handler uses the internal HTTP wrapper to send the HTTP request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		// check whether scheme is supported
		$supportedWrappers = array_intersect(array('http', 'https'), stream_get_wrappers());

		if(!in_array($request->getUri()->getScheme(), $supportedWrappers))
		{
			throw new HandlerException('Unsupported stream wrapper');
		}

		// create context
		$context = stream_context_create();

		// assign http context parameters
		self::assignHttpContext($context, $request, $options);

		// disable follow location if not available
		if(!$this->hasFollowLocation)
		{
			stream_context_set_option($context, 'http', 'follow_location', 0);
		}

		// set ssl
		if($options->getSsl() !== false && ($options->getSsl() === true || strcasecmp($request->getUri()->getScheme(), 'https') === 0))
		{
			self::assignSslContext($context, $options);
		}

		// callback
		$callback = $options->getCallback();

		if(!empty($callback))
		{
			call_user_func_array($callback, array($context, $request));
		}

		// open socket
		set_error_handler(__CLASS__ . '::handleError');
		$handle = fopen($request->getUri()->toString(), 'r', false, $context);
		restore_error_handler();

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
		restore_error_handler();

		throw new HandlerException($errstr, $errno);
	}

	public static function assignHttpContext($context, Request $request, Options $options = null)
	{
		stream_context_set_option($context, 'http', 'method', $request->getMethod());
		stream_context_set_option($context, 'http', 'protocol_version', $request->getProtocolVersion() ?: 1.1);

		// until chunked transfer encoding if fully implemented we remove the
		// header
		if($request->hasHeader('Transfer-Encoding'))
		{
			$request->removeHeader('Transfer-Encoding');
		}

		// set header
		$headers = implode(Http::$newLine, ResponseParser::buildHeaderFromMessage($request));

		stream_context_set_option($context, 'http', 'header', $headers);

		// set body
		$body = $request->getBody();

		if($body !== null && !in_array($request->getMethod(), array('HEAD', 'GET')))
		{
			stream_context_set_option($context, 'http', 'content', (string) $body);
		}

		if($options !== null)
		{
			// set proxy
			$proxy = $options->getProxy();

			if(!empty($proxy))
			{
				stream_context_set_option($context, 'http', 'proxy', $proxy);
			}

			// set follow location
			stream_context_set_option($context, 'http', 'follow_location', (int) $options->getFollowLocation());
			stream_context_set_option($context, 'http', 'max_redirects', $options->getMaxRedirects());

			// set timeout
			$timeout = $options->getTimeout();

			if(!empty($timeout))
			{
				stream_context_set_option($context, 'http', 'timeout', $timeout);
			}
		}
	}

	public static function assignSslContext($context, Options $options)
	{
		$caPath = $options->getCaPath();

		if(!empty($caPath))
		{
			stream_context_set_option($context, 'ssl', 'verify_peer', true);

			if(is_file($caPath))
			{
				stream_context_set_option($context, 'ssl', 'cafile', $caPath);
			}
			else if(is_dir($caPath))
			{
				stream_context_set_option($context, 'ssl', 'capath', $caPath);
			}
		}
		else
		{
			stream_context_set_option($context, 'ssl', 'verify_peer', false);
		}
	}
}


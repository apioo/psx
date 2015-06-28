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

namespace PSX;

use InvalidArgumentException;
use PSX\Http\Cookie;
use PSX\Http\CookieStoreInterface;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Curl;
use PSX\Http\HandlerInterface;
use PSX\Http\Options;
use PSX\Http\RedirectException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Util\UriResolver;

/**
 * This class offers a simple way to make http requests. It can use either curl
 * or fsockopen handler to send the request. Here an example of an basic GET 
 * request
 * <code>
 * $http     = new Http();
 * $request  = new GetRequest('http://google.com');
 * $response = $http->request($request);
 *
 * if($response->getStatusCode() == 200)
 * {
 *   echo (string) $response->getBody();
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Http
{
	/**
	 * @var string
	 */
	public static $newLine = "\r\n";

	/**
	 * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 * @var array
	 */
	public static $codes   = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing', // RFC2518
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status', // RFC4918
		208 => 'Already Reported', // RFC5842
		226 => 'IM Used', // RFC3229
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect', // RFC7238
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot', // RFC2324
		422 => 'Unprocessable Entity', // RFC4918
		423 => 'Locked', // RFC4918
		424 => 'Failed Dependency', // RFC4918
		425 => 'Reserved for WebDAV advanced collections expired proposal', // RFC2817
		426 => 'Upgrade Required', // RFC2817
		428 => 'Precondition Required', // RFC6585
		429 => 'Too Many Requests', // RFC6585
		431 => 'Request Header Fields Too Large', // RFC6585
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates (Experimental)', // RFC2295
		507 => 'Insufficient Storage', // RFC4918
		508 => 'Loop Detected', // RFC5842
		510 => 'Not Extended', // RFC2774
		511 => 'Network Authentication Required', // RFC6585
	);

	/**
	 * @var PSX\Http\HandlerInterface
	 */
	protected $handler;

	/**
	 * @var PSX\Http\CookieStoreInterface
	 */
	protected $cookieStore;

	/**
	 * If no handler is defined the curl handler is used as fallback
	 *
	 * @param PSX\Http\HandlerInterface $handler
	 */
	public function __construct(HandlerInterface $handler = null)
	{
		$this->handler = $handler !== null ? $handler : new Curl();
	}

	/**
	 * Sends the request through the given handler and returns the response
	 *
	 * @param PSX\Http\Request $request
	 * @param PSX\Http\Options $options
	 * @return PSX\Http\Response
	 */
	public function request(Request $request, Options $options = null, $count = 0)
	{
		if(!$request->getUri()->isAbsolute())
		{
			throw new InvalidArgumentException('Request url must be absolute');
		}

		// set cookie headers
		if($this->cookieStore !== null)
		{
			$cookies = $this->cookieStore->load($request->getUri()->getHost());

			if(!empty($cookies))
			{
				$kv = array();

				foreach($cookies as $cookie)
				{
					$path = ltrim($cookie->getPath(), '/');

					if($cookie->getExpires() !== null && $cookie->getExpires()->getTimestamp() < time())
					{
						$this->cookieStore->remove($request->getUri()->getHost(), $cookie);
					}
					else if($cookie->getPath() !== null && substr($request->getUri()->getPath(), 0, strlen($path)) != $path)
					{
						// path does not fit
					}
					else
					{
						$kv[] = $cookie->getName() . '=' . $cookie->getValue();
					}
				}

				$request->addHeader('Cookie', implode('; ', $kv));
			}
		}

		// set content length
		$body = $request->getBody();

		if($body !== null && $request->hasHeader('Transfer-Encoding') != 'chunked' && !in_array($request->getMethod(), array('HEAD', 'GET')))
		{
			$size = $body->getSize();

			if($size !== false)
			{
				$request->setHeader('Content-Length', $size);
			}
		}

		// set default options
		if($options === null)
		{
			$options = new Options();
		}

		// make request
		$response = $this->handler->request($request, $options);

		// store cookies
		if($this->cookieStore !== null)
		{
			$cookies = $response->getHeaderLines('Set-Cookie');

			foreach($cookies as $rawCookie)
			{
				try
				{
					$cookie = new Cookie($rawCookie);
					$domain = $cookie->getDomain() !== null ? $cookie->getDomain() : $request->getUri()->getHost();

					$this->cookieStore->store($domain, $cookie);
				}
				catch(InvalidArgumentException $e)
				{
					// invalid cookies
				}
			}
		}

		// check follow location
		if($options->getFollowLocation() && ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400))
		{
			$location = (string) $response->getHeader('Location');

			if(!empty($location) && $location != $request->getUri()->toString())
			{
				if($options->getMaxRedirects() > $count)
				{
					$location = UriResolver::resolve($request->getUri(), new Uri($location));

					return $this->request(new GetRequest($location), $options, ++$count);
				}
				else
				{
					throw new RedirectException('Max redirection reached');
				}
			}
		}

		return $response;
	}

	/**
	 * Sets the handler
	 *
	 * @param PSX\Http\HandlerInterface $handler
	 * @return void
	 */
	public function setHandler(HandlerInterface $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * Returns the handler
	 *
	 * @return PSX\Http\HandlerInterface
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * Sets an cookie store
	 *
	 * @param PSX\Http\CookieStoreInterface
	 */
	public function setCookieStore(CookieStoreInterface $cookieStore)
	{
		$this->cookieStore = $cookieStore;
	}

	/**
	 * Returns the cookie store
	 *
	 * @return PSX\Http\CookieStoreInterface
	 */
	public function getCookieStore()
	{
		return $this->cookieStore;
	}
}


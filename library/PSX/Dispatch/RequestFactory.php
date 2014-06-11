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

use PSX\Config;
use PSX\Http\Request;
use PSX\Http\Stream\TempStream;
use PSX\Url;
use UnexpectedValueException;

/**
 * RequestFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestFactory implements RequestFactoryInterface
{
	protected $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * If the psx_url from the config contains an path in the url this path gets
	 * removed from the request url. Because of this you can have an psx project
	 * also in an sub folder
	 *
	 * @return Psr\HttpMessage\RequestInterface
	 */
	public function createRequest()
	{
		$parts = parse_url($this->config['psx_url']);

		if($parts !== false && isset($parts['scheme']) && isset($parts['host']))
		{
			$port = !empty($parts['port']) ? ':' . $parts['port'] : '';

			if(!$this->isCli())
			{
				$script = isset($_SERVER['SCRIPT_NAME'])  ? $_SERVER['SCRIPT_NAME']  : null;
				$info   = isset($_SERVER['PATH_INFO'])    ? $_SERVER['PATH_INFO']    : null;
				$query  = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
				$path   = $script . $info;

				if(!empty($query))
				{
					$path.= '?' . $query;
				}

				if(isset($parts['path']))
				{
					$path = substr($path, strlen($parts['path']) + strlen($this->config['psx_dispatch']));
				}
			}
			else
			{
				$path = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '/';
			}

			$self = $parts['scheme'] . '://' . $parts['host'] . $port . $path;

			// create request
			$url     = new Url($self);
			$method  = $this->getRequestMethod();
			$headers = $this->getRequestHeaders();
			$body    = new TempStream(fopen('php://input', 'r'));

			return new Request($url, $method, $headers, $body);
		}
		else
		{
			throw new UnexpectedValueException('Invalid PSX url');
		}
	}

	/**
	 * Tries to detect the current request method. It considers the
	 * X-HTTP-METHOD-OVERRIDE header.
	 *
	 * @return string
	 */
	protected function getRequestMethod()
	{
		if(isset($_SERVER['REQUEST_METHOD']))
		{
			// check for X-HTTP-Method-Override
			if(isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) && 
				in_array($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'], array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE', 'CONNECT')))
			{
				return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
			}
			else
			{
				return $_SERVER['REQUEST_METHOD'];
			}
		}
		else
		{
			return 'GET';
		}
	}

	/**
	 * Returns all request headers
	 *
	 * @return array
	 */
	protected function getRequestHeaders()
	{
		$contentKeys = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
		$headers     = array();

		foreach($_SERVER as $key => $value)
		{
			if(strpos($key, 'HTTP_') === 0)
			{
				$headers[str_replace('_', '-', substr($key, 5))] = $value;
			}
			else if(isset($contentKeys[$key]))
			{
				$headers[str_replace('_', '-', $key)] = $value;
			}
		}

		if(!isset($headers['AUTHORIZATION']))
		{
			if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
			{
				$headers['AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			}
			else if(isset($_SERVER['PHP_AUTH_USER']))
			{
				$headers['AUTHORIZATION'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : ''));
			}
			else if(isset($_SERVER['PHP_AUTH_DIGEST']))
			{
				$headers['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
			}
		}

		return $headers;
	}

	/**
	 * Returns whether we are in CLI mode or not
	 *
	 * @return boolean
	 */
	protected function isCli()
	{
		return PHP_SAPI == 'cli';
	}
}

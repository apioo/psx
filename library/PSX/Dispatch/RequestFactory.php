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
	 * removed from the REQUEST_URI. Because of this you can have an psx project 
	 * also in an sub folder on the web server
	 *
	 * @return PSX\Http\Request
	 */
	public function createRequest()
	{
		$parts = parse_url($this->config['psx_url']);

		if($parts !== false && isset($parts['scheme']) && isset($parts['host']))
		{
			$port = !empty($parts['port']) ? ':' . $parts['port'] : '';
			$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
			$path = substr($path, strlen($parts['path']) + strlen($this->config['psx_dispatch']));
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
	public function getRequestMethod()
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
	public function getRequestHeaders()
	{
		if(function_exists('apache_request_headers'))
		{
			return apache_request_headers();
		}
		else
		{
			$headers = array();

			foreach($_SERVER as $key => $value)
			{
				if(substr($key, 0, 5) == 'HTTP_')
				{
					$key = str_replace('_', '-', substr($key, 5));

					$headers[$key] = $value;
				}
			}

			return $headers;
		}
	}
}

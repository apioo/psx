<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use Closure;
use PSX\Exception;
use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\RedirectException;
use PSX\Http\Request;
use PSX\Http\Response;

/**
 * Mock handler where you can register urls wich return a specific response on 
 * request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Mock implements HandlerInterface
{
	protected $resources;

	public function __construct()
	{
		$this->resources = array();
	}

	public function getResources()
	{
		return $this->resources;
	}

	public function add($method, $url, Closure $handler)
	{
		if(!in_array($method, array('GET', 'POST', 'PUT', 'DELETE')))
		{
			throw new Exception('Invalid http request method');
		}

		foreach($this->resources as $resource)
		{
			if($resource['method'] == $method && $resource['url'] == $url)
			{
				throw new Exception('Resource already exists');
			}
		}

		$this->resources[] = array(
			'method'  => $method,
			'url'     => $url,
			'handler' => $handler,
		);;
	}

	public function request(Request $request, $count = 0)
	{
		$url = $request->getUrl()->__toString();

		foreach($this->resources as $resource)
		{
			if($resource['method'] == $request->getMethod() && $resource['url'] == $url)
			{
				$response = $resource['handler']($request);

				return Response::convert($response);
			}
		}

		throw new Exception('Resource not available ' . $request->getMethod() . ' ' . $url);
	}

	public static function getByXmlDefinition($file)
	{
		if(!is_file($file))
		{
			throw new Exception('Is not a file');
		}

		$mock = new self();
		$xml  = simplexml_load_file($file);

		foreach($xml->resource as $resource)
		{
			$method   = (string) $resource->method;
			$url      = (string) $resource->url;
			$response = (string) $resource->response;
			$response = base64_decode($response);

			$mock->add($method, $url, function($request) use ($response){
				return $response;
			});
		}

		return $mock;
	}
}

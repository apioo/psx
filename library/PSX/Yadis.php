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

namespace PSX;

use PSX\Http\GetRequest;
use PSX\Html\Parse;
use PSX\Html\Parse\Element;

/**
 * Yadis
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Yadis
{
	private $maxRecursion;
	private $http;

	public function __construct(Http $http, $recursion = 3)
	{
		$this->http = $http;

		$this->setMaxRecursion($recursion);
	}

	public function setMaxRecursion($recursion)
	{
		$this->maxRecursion = (integer) $recursion;
	}

	public function discover(Url $url, $raw = false, $deep = 0)
	{
		if($this->maxRecursion >= $deep)
		{
			$response = $this->request($url);
			$header   = $response->getHeader();

			// x-xrds-location
			if(isset($header['x-xrds-location']))
			{
				$location = new Url($header['x-xrds-location']);

				if(strcasecmp($url, $location) != 0)
				{
					return $this->discover($location, $raw, $deep++);
				}
			}

			// application/xrds+xml
			// we check whether the content type contains the string "xml" i.e.
			// text/xml or application/xrds+xml ... after the specification we
			// only must check for the content-type application/xrds+xml but
			// some websites serve the xrds document as text/xml
			if(isset($header['content-type']) && strpos($header['content-type'], 'xml') !== false)
			{
				if($raw === true)
				{
					return $response->getBody();
				}
				else
				{
					return $this->parse($response->getBody());
				}
			}

			// search for <meta /> tag
			$parse    = new Parse($response->getBody());
			$element  = new Element('meta', array('http-equiv' => 'X-XRDS-Location'));

			$location = $parse->fetchAttrFromHead($element, 'content');

			if(!empty($location))
			{
				return $this->discover($location, $raw, $deep++);
			}

			// we dont find anything
			return false;
		}
		else
		{
			throw new Exception('Max recurison level reached');
		}
	}

	public function parse($xrds)
	{
		$xml = simplexml_load_string($xrds);

		if($xml !== false)
		{
			$redirect = isset($xml['redirect']) ? strval($xml['redirect']) : false;
			$ref      = isset($xml['ref'])      ? strval($xml['ref'])      : false;

			if(isset($xml->XRD))
			{
				$xrd = new Xrd($xml->XRD);

				return $xrd;
			}
		}
		else
		{
			throw new Exception('We dont receive an valid XRDS document');
		}
	}

	public function request(Url $url)
	{
		$request = new GetRequest($url, array(
			'Accept'     => 'application/xrds+xml',
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION,
		));
		$request->setFollowLocation(true);

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			return $response;
		}
		else
		{
			throw new Exception($lastError);
		}
	}
}

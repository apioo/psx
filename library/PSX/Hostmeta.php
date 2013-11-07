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

use PSX\Hostmeta\Jrd;
use PSX\Hostmeta\Xrd;
use PSX\Http\GetRequest;

/**
 * Hostmeta
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc7033
 */
class Hostmeta
{
	private $http;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Makes an GET request to the "/.well-known/host-meta" of the host. Then
	 * tries to parse the response as XRD element. The xrd must have an Host tag
	 * wich has the same host as the $url.
	 *
	 * @return PSX\Hostmeta\Document
	 */
	public function discover(Url $url)
	{
		$url = $url->getScheme() . '://' . $url->getHost() . '/.well-known/host-meta';
		$url = new Url($url);

		return self::requestJrd($this->http, $url);
	}

	/**
	 * Method wich tries to request an jrd document from the given url. Used 
	 * also in the webfinger class therefore it is here static
	 *
	 * @return PSX\Hostmeta\Document
	 */
	public static function requestJrd(Http $http, Url $url)
	{
		$request  = new GetRequest($url, array(
			'Accept'     => 'application/jrd+json',
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION,
		));
		$request->setFollowLocation(true);
		$response = $http->request($request);

		if($response->getCode() == 200)
		{
			$contentType = $response->getHeader('Content-Type');

			if(strpos($contentType, 'application/jrd+json') !== false || 
				strpos($contentType, 'application/json') !== false)
			{
				$jrd = new Jrd();
				$jrd->import(Json::decode($response->getBody()));

				return $jrd;
			}
			else if(strpos($contentType, 'application/xrd+xml') !== false || 
				strpos($contentType, 'application/xml') !== false)
			{
				$xrd = new Xrd();
				$xrd->import(simplexml_load_string($response->getBody()));

				return $xrd;
			}
			else
			{
				throw new Exception('Received unknown content type');
			}
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getCode() . ' from ' . strval($url));
		}
	}
}

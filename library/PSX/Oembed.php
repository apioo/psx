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

use PSX\Data\Reader;
use PSX\Html\Parse;
use PSX\Html\Parse\Element;
use PSX\Http\GetRequest;
use PSX\Oembed\TypeAbstract;

/**
 * Oembed
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Oembed
{
	private $http;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Requests the $url and tries to parse the response as oembed type. The url
	 * must be pointing to an oembed provider i.e.:
	 * http://flickr.com/services/oembed?url=http%3A//flickr.com/photos/bees/2362225867/
	 *
	 * @param PSX\Url $url
	 * @return PSX\Oembed\TypeAbstract
	 */
	public function request(Url $url)
	{
		if(!$url->issetParam('url'))
		{
			throw new Exception('Required parameter url missing');
		}

		$format   = $url->addParam('format', 'json');
		$request  = new GetRequest($url, array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		));
		$response = $this->http->request($request);

		if($response->getCode() >= 200 && $response->getCode() < 300)
		{
			switch($url->getParam('format'))
			{
				case 'json':
					$reader = new Reader\Json();
					$result = $reader->read($response);
					break;

				case 'xml':
					$reader = new Reader\Xml();
					$result = $reader->read($response);
					break;

				default:

					throw new Exception('Invalid format');
					break;
			}

			return TypeAbstract::factory($result);
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getCode());
		}
	}

	/**
	 * Tries to discover an oembed link from an html page. Returns the
	 * discovered oembed object
	 *
	 * @param PSX\Url $url
	 * @return PSX\Oembed\TypeAbstract
	 */
	public function discover(Url $url)
	{
		$request  = new GetRequest($url, array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		));
		$response = $this->http->request($request);

		if($response->getCode() >= 200 && $response->getCode() < 300)
		{
			return $this->request(new Url(self::findTag($response->getBody())));
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getCode());
		}
	}

	public static function findTag($content)
	{
		$parse   = new Parse($content);
		$element = new Element('link', array('rel' => 'alternate', 'type' => 'application/json+oembed'));
		$href    = $parse->fetchAttrFromHead($element, 'href');

		if(empty($href))
		{
			$element = new Element('link', array('rel' => 'alternate', 'type' => 'text/xml+oembed'));
			$href    = $parse->fetchAttrFromHead($element, 'href');

			if(empty($href))
			{
				throw new Exception('Could not discover oembed link');
			}
		}

		return $href;
	}
}

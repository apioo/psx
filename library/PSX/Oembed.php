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

namespace PSX;

use PSX\Data\ReaderInterface;
use PSX\Data\InvalidDataException;
use PSX\Data\Importer;
use PSX\Exception;
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
	protected $http;
	protected $importer;

	public function __construct(Http $http, Importer $importer)
	{
		$this->http     = $http;
		$this->importer = $importer;
	}

	/**
	 * Requests the $url and tries to parse the response as oembed type. The url
	 * must be pointing to an oembed provider i.e.:
	 * http://flickr.com/services/oembed?url=http://www.flickr.com/photos/neilio/20403964/
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
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION,
			'Accept'     => 'application/json'
		));
		$response = $this->http->request($request);

		if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)
		{
			$source = function(array $data){

				$type = isset($data['type']) ? strtolower($data['type']) : null;
				
				if(in_array($type, array('link', 'photo', 'rich', 'video')))
				{
					$class = 'PSX\\Oembed\\Type\\' . ucfirst($type);

					if(class_exists($class))
					{
						return new $class();
					}
					else
					{
						throw new Exception('Class "' . $class . '" does not exist');
					}
				}
				else
				{
					throw new InvalidDataException('Invalid type');
				}

			};

			return $this->importer->import($source, $response, null, ReaderInterface::JSON);
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getStatusCode());
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

		if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)
		{
			return $this->request(new Url(self::findTag((string) $response->getBody())));
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getStatusCode());
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

<?php
/*
 *  $Id: Oembed.php 663 2012-10-07 16:45:52Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Oembed
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Oembed
 * @version    $Revision: 663 $
 */
class PSX_Oembed
{
	private $http;

	public function __construct(PSX_Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Requests the $url and tries to parse the response as oembed type. The url
	 * must be pointing to an oembed provider i.e.:
	 * http://flickr.com/services/oembed?url=http%3A//flickr.com/photos/bees/2362225867/
	 *
	 * @param PSX_Url $url
	 * @return PSX_Oembed_TypeAbstract
	 */
	public function request(PSX_Url $url)
	{
		if(!$url->issetParam('url'))
		{
			throw new PSX_Oembed_Exception('Required parameter url missing');
		}

		$format   = $url->addParam('format', 'json');
		$request  = new PSX_Http_GetRequest($url, array(
			'User-Agent' => __CLASS__ . ' ' . PSX_Base::VERSION
		));
		$response = $this->http->request($request);

		if($response->getCode() >= 200 && $response->getCode() < 300)
		{
			switch($url->getParam('format'))
			{
				case 'json':

					$reader = new PSX_Data_Reader_Json();
					$result = $reader->read($response);
					break;

				case 'xml':

					$reader = new PSX_Data_Reader_Xml();
					$result = $reader->read($response);
					break;

				default:

					throw new PSX_Oembed_Exception('Invalid format');
					break;
			}

			return PSX_Oembed_TypeAbstract::factory($result);
		}
		else
		{
			throw new PSX_Oembed_Exception('Invalid response code ' . $response->getCode());
		}
	}

	/**
	 * Tries to discover an oembed link from an html page. Returns the
	 * discovered oembed object
	 *
	 * @param PSX_Url $url
	 * @return PSX_Oembed_TypeAbstract
	 */
	public function discover(PSX_Url $url)
	{
		$request  = new PSX_Http_GetRequest($url, array(
			'User-Agent' => __CLASS__ . ' ' . PSX_Base::VERSION
		));
		$response = $this->http->request($request);

		if($response->getCode() >= 200 && $response->getCode() < 300)
		{
			return $this->request(new PSX_Url(self::findTag($response->getBody())));
		}
		else
		{
			throw new PSX_Oembed_Exception('Invalid response code ' . $response->getCode());
		}
	}

	public static function findTag($content)
	{
		$parse   = new PSX_Html_Parse($content);

		$element = new PSX_Html_Parse_Element('link', array('rel' => 'alternate', 'type' => 'application/json+oembed'));
		$href    = $parse->fetchAttrFromHead($element, 'href');

		if(empty($href))
		{
			$element = new PSX_Html_Parse_Element('link', array('rel' => 'alternate', 'type' => 'text/xml+oembed'));
			$href    = $parse->fetchAttrFromHead($element, 'href');

			if(empty($href))
			{
				throw new PSX_Oembed_Exception('Could not discover oembed link');
			}
		}

		return $href;
	}
}

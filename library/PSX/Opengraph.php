<?php
/*
 *  $Id: Opengraph.php 619 2012-08-25 11:17:06Z k42b3.x@googlemail.com $
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

namespace PSX;

use PSX\Html\Parse;
use PSX\Html\Parse\Element;
use PSX\Http\GetRequest;

/**
 * Discovers opengraph tags on an specific url. The discovery method make an GET
 * request to the specified url and tries to fetch all opengraph tags from the
 * response body. You can also specifiy wich tags should be fetched in example:
 * <code>
 * $http = new PSX_Http(new PSX_Http_Handler_Curl());
 * $og   = new PSX_Opengraph($http)
 *
 * $tags = $og->discover(new PSX_Url('http://opengraphprotocol.org/'));
 *
 * $tags = $og->discover(new PSX_Url('http://opengraphprotocol.org/'), PSX_Opengraph::TITLE | PSX_Opengraph::TYPE);
 * </code>
 *
 * The discover method returns an associative array wich has as key the meta
 * name without "og:" and "-" are replaced with "_". As value either false if
 * the tag was not found or the value of the content attribute.
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @see        http://opengraphprotocol.org
 * @category   PSX
 * @package    PSX_Opengraph
 * @version    $Revision: 619 $
 */
class Opengraph
{
	const TITLE          = 0x1;
	const TYPE           = 0x2;
	const URL            = 0x4;
	const IMAGE          = 0x8;
	const DESCRIPTION    = 0x10;
	const SITE_NAME      = 0x20;
	const LATITUDE       = 0x40;
	const LONGITUDE      = 0x80;
	const STREET_ADDRESS = 0x100;
	const LOCALITY       = 0x200;
	const REGION         = 0x400;
	const POSTAL_CODE    = 0x800;
	const COUNTRY_NAME   = 0x1000;
	const ALL            = 0x2000;

	private $metadata = array(

		'title'         => 'og:title',
		'type'          => 'og:type',
		'url'           => 'og:url',
		'image'         => 'og:image',
		'description'   => 'og:description',
		'siteName'      => 'og:site_name',
		'latitude'      => 'og:latitude',
		'longitude'     => 'og:longitude',
		'streetAddress' => 'og:street-address',
		'locality'      => 'og:locality',
		'region'        => 'og:region',
		'postalCode'    => 'og:postal-code',
		'countryName'   => 'og:country-name'

	);

	private $http;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	public function discover(Url $url, $type = Opengraph::ALL)
	{
		$request   = new GetRequest($url);

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$data   = array();
			$fields = array();
			$parser = new Parse($response->getBody());

			if($type & self::TITLE)          { $fields['title']         = $this->metadata['title']; }
			if($type & self::TYPE)           { $fields['type']          = $this->metadata['type']; }
			if($type & self::URL)            { $fields['url']           = $this->metadata['url']; }
			if($type & self::IMAGE)          { $fields['image']         = $this->metadata['image']; }
			if($type & self::DESCRIPTION)    { $fields['description']   = $this->metadata['description']; }
			if($type & self::SITE_NAME)      { $fields['siteName']      = $this->metadata['siteName']; }
			if($type & self::LATITUDE)       { $fields['latitude']      = $this->metadata['latitude']; }
			if($type & self::LONGITUDE)      { $fields['longitude']     = $this->metadata['longitude']; }
			if($type & self::STREET_ADDRESS) { $fields['streetAddress'] = $this->metadata['streetAddress']; }
			if($type & self::LOCALITY)       { $fields['locality']      = $this->metadata['locality']; }
			if($type & self::REGION)         { $fields['region']        = $this->metadata['region']; }
			if($type & self::POSTAL_CODE)    { $fields['postalCode']    = $this->metadata['postalCode']; }
			if($type & self::COUNTRY_NAME)   { $fields['countryName']   = $this->metadata['countryName']; }
			if($type & self::ALL)            { $fields                  = $this->metadata; }

			$element = new Element('meta');

			foreach($fields as $key => $meta)
			{
				$element->setAttributes(array('property' => $meta));

				$value = $parser->fetchAttrFromHead($element, 'content');

				$data[$key] = $value;
			}

			return $data;
		}
		else
		{
			throw new Exception($lastError);
		}
	}
}


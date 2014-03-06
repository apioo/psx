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

use PSX\Html\Lexer;
use PSX\Html\Lexer\Token;
use PSX\Http\GetRequest;
use PSX\Opengraph\Attribute;

/**
 * Discovers opengraph tags on an specific url. The discovery method make an GET
 * request to the specified url and tries to fetch all opengraph tags from the
 * response body
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://opengraphprotocol.org
 */
class Opengraph
{
	protected $http;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	public function discover(Url $url)
	{
		$request  = new GetRequest($url);
		$response = $this->http->request($request);

		$root = Lexer::parse((string) $response->getBody());
		$data = array();

		if($root instanceof Token\Element)
		{
			$elements = $root->getElementsByTagName('meta');

			foreach($elements as $element)
			{
				$attributes = $element->getAttributes();

				if(isset($attributes['property']))
				{
					$data[$attributes['property']] = $attributes['content'];
				}
			}
		}

		return new Attribute($data);
	}
}

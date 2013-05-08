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

namespace PSX\Http;

use PSX\Url;

/**
 * PutRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PutRequest extends Request
{
	/**
	 * __construct
	 *
	 * @param PSX\Url|string $url
	 * @param array $header
	 * @param string $body
	 * @param boolean $override
	 */
	public function __construct($url, array $header = array(), $body = null, $override = false)
	{
		$url    = $url instanceof Url ? $url : new Url((string) $url);
		$method = $override ? 'POST' : 'PUT';
		$header = self::mergeHeader(array(

			'Host'   => $url->getHost(),
			'Expect' => '',

		), $header);


		$isFormUrlencoded = false;

		if(is_array($body))
		{
			$isFormUrlencoded = true;

			$body = http_build_query($body, '', '&');
		}


		parent::__construct($url, $method, $header, $body);


		if($isFormUrlencoded)
		{
			$this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
		}


		if($override)
		{
			$this->addHeader('X-HTTP-Method-Override', 'PUT');
		}
	}

	public function setBody($body)
	{
		$body = (string) $body;
		$len  = strlen($body);

		if($len > 0)
		{
			$this->addHeader('Content-Length', $len);

			$this->body = $body;
		}
	}
}



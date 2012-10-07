<?php
/*
 *  $Id: PostRequest.php 579 2012-08-14 18:22:10Z k42b3.x@googlemail.com $
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
 * PSX_Http_PostRequest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Http
 * @version    $Revision: 579 $
 */
class PSX_Http_PostRequest extends PSX_Http_Request
{
	/**
	 * __construct
	 *
	 * @param PSX_Url|string $url
	 * @param array $header
	 * @param array $body
	 * @return PSX_Http_PostRequest
	 */
	public function __construct($url, array $header = array(), $body = null)
	{
		$url    = $url instanceof PSX_Url ? $url : new PSX_Url((string) $url);
		$method = 'POST';
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


<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use InvalidArgumentException;

/**
 * Represents an URL. A string is only an valid URL if it has an scheme and host
 * part. If the URL is not valid an exception gets thrown. Note if you want 
 * display an URL you need to escape the URL according to the context. I.e. to 
 * display the URL in an HTML context it is nessacary to use htmlspecialchars 
 * since the URL could contain an XSS vector
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Url extends Uri
{
	public function setPort($port)
	{
		$port = (int) $port;

		if($port < 1 || $port > 0xFFFF)
		{
			throw new InvalidArgumentException('Invalid port range');
		}

		parent::setPort($port);
	}

	protected function parse($url)
	{
		$url = (string) $url;

		// append http scheme for urls starting with //. Normally // means that
		// we use the scheme from the base url but in this context there is no
		// base url available so we assume http
		if(substr($url, 0, 2) == '//')
		{
			$url = 'http:' . $url;
		}

		parent::parse($url);

		// we need at least an scheme and host
		if(empty($this->scheme) || empty($this->host))
		{
			throw new InvalidArgumentException('Invalid url syntax');
		}
	}
}

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

namespace PSX\Http\Exception;

/**
 * This class of status code indicates that further action needs to be taken by 
 * the user agent in order to fulfill the request. The action required MAY be 
 * carried out by the user agent without interaction with the user if and only 
 * if the method used in the second request is GET or HEAD. A client SHOULD 
 * detect infinite redirection loops, since such loops generate network traffic 
 * for each redirection.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RedirectionException extends StatusCodeException
{
	protected $location;

	public function __construct($statusCode, $location = null)
	{
		parent::__construct('Redirect exception', $statusCode);

		$this->location = $location;
	}

	public function getLocation()
	{
		return $this->location;
	}
}

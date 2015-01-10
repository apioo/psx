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

namespace PSX\Http\Exception;

/**
 * The server has not found anything matching the Request-URI. No indication is 
 * given of whether the condition is temporary or permanent. The 410 (Gone) 
 * status code SHOULD be used if the server knows, through some internally 
 * configurable mechanism, that an old resource is permanently unavailable and 
 * has no forwarding address. This status code is commonly used when the server 
 * does not wish to reveal exactly why the request has been refused, or when no 
 * other response is applicable.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class NotFoundException extends ClientErrorException
{
	public function __construct($message)
	{
		parent::__construct($message, 404);
	}
}

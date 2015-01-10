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
 * The response to the request can be found under a different URI and SHOULD be 
 * retrieved using a GET method on that resource. This method exists primarily 
 * to allow the output of a POST-activated script to redirect the user agent to 
 * a selected resource. The new URI is not a substitute reference for the 
 * originally requested resource. The 303 response MUST NOT be cached, but the 
 * response to the second (redirected) request might be cacheable.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SeeOtherException extends RedirectionException
{
	public function __construct($location)
	{
		parent::__construct(303, $location);
	}
}

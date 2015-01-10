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
 * The server understood the request, but is refusing to fulfill it. 
 * Authorization will not help and the request SHOULD NOT be repeated. If the 
 * request method was not HEAD and the server wishes to make public why the 
 * request has not been fulfilled, it SHOULD describe the reason for the refusal 
 * in the entity. If the server does not wish to make this information available 
 * to the client, the status code 404 (Not Found) can be used instead.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ForbiddenException extends ClientErrorException
{
	public function __construct($message)
	{
		parent::__construct($message, 403);
	}
}

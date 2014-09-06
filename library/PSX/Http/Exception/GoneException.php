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
 * The requested resource is no longer available at the server and no forwarding 
 * address is known. This condition is expected to be considered permanent. 
 * Clients with link editing capabilities SHOULD delete references to the 
 * Request-URI after user approval. If the server does not know, or has no 
 * facility to determine, whether or not the condition is permanent, the status 
 * code 404 (Not Found) SHOULD be used instead. This response is cacheable 
 * unless indicated otherwise.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GoneException extends ClientErrorException
{
	public function __construct($message)
	{
		parent::__construct($message, 410);
	}
}

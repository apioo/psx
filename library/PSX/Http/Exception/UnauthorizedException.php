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
 * The request requires user authentication. The response MUST include a 
 * WWW-Authenticate header field (section 14.47) containing a challenge 
 * applicable to the requested resource. The client MAY repeat the request with 
 * a suitable Authorization header field (section 14.8). If the request already 
 * included Authorization credentials, then the 401 response indicates that 
 * authorization has been refused for those credentials. If the 401 response 
 * contains the same challenge as the prior response, and the user agent has 
 * already attempted authentication at least once, then the user SHOULD be 
 * presented the entity that was given in the response, since that entity might 
 * include relevant diagnostic information.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class UnauthorizedException extends ClientErrorException
{
	protected $type;
	protected $parameters;

	public function __construct($message, $type, array $parameters = array())
	{
		parent::__construct($message, 401);

		$this->type       = $type;
		$this->parameters = $parameters;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getParameters()
	{
		return $this->parameters;
	}
}

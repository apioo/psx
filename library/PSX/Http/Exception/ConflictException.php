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
 * The request could not be completed due to a conflict with the current state 
 * of the resource. This code is only allowed in situations where it is expected 
 * that the user might be able to resolve the conflict and resubmit the request. 
 * The response body SHOULD include enough information for the user to recognize 
 * the source of the conflict. Ideally, the response entity would include enough 
 * information for the user or user agent to fix the problem; however, that 
 * might not be possible and is not required.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ConflictException extends ClientErrorException
{
	public function __construct($message)
	{
		parent::__construct($message, 409);
	}
}

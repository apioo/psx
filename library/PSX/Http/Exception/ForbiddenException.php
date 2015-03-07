<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ForbiddenException extends ClientErrorException
{
	public function __construct($message)
	{
		parent::__construct($message, 403);
	}
}

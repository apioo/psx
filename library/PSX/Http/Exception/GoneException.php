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
 * The requested resource is no longer available at the server and no forwarding 
 * address is known. This condition is expected to be considered permanent. 
 * Clients with link editing capabilities SHOULD delete references to the 
 * Request-URI after user approval. If the server does not know, or has no 
 * facility to determine, whether or not the condition is permanent, the status 
 * code 404 (Not Found) SHOULD be used instead. This response is cacheable 
 * unless indicated otherwise.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GoneException extends ClientErrorException
{
	public function __construct($message)
	{
		parent::__construct($message, 410);
	}
}

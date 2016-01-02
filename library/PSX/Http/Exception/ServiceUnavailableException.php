<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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
 * The server is currently unable to handle the request due to a temporary
 * overloading or maintenance of the server. The implication is that this is a
 * temporary condition which will be alleviated after some delay. If known, the
 * length of the delay MAY be indicated in a Retry-After header. If no
 * Retry-After is given, the client SHOULD handle the response as it would for a
 * 500 response.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServiceUnavailableException extends ServerErrorException
{
    public function __construct($message)
    {
        parent::__construct($message, 503);
    }
}

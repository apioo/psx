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
 * The response to the request can be found under a different URI and SHOULD be
 * retrieved using a GET method on that resource. This method exists primarily
 * to allow the output of a POST-activated script to redirect the user agent to
 * a selected resource. The new URI is not a substitute reference for the
 * originally requested resource. The 303 response MUST NOT be cached, but the
 * response to the second (redirected) request might be cacheable.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SeeOtherException extends RedirectionException
{
    public function __construct($location)
    {
        parent::__construct(303, $location);
    }
}

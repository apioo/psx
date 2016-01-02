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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

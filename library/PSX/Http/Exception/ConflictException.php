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
 * The request could not be completed due to a conflict with the current state
 * of the resource. This code is only allowed in situations where it is expected
 * that the user might be able to resolve the conflict and resubmit the request.
 * The response body SHOULD include enough information for the user to recognize
 * the source of the conflict. Ideally, the response entity would include enough
 * information for the user or user agent to fix the problem; however, that
 * might not be possible and is not required.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConflictException extends ClientErrorException
{
    public function __construct($message)
    {
        parent::__construct($message, 409);
    }
}

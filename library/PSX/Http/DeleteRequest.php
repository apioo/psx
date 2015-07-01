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

namespace PSX\Http;

use PSX\Url;

/**
 * DeleteRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DeleteRequest extends Request
{
    /**
     * @param \PSX\Url|string $url
     * @param array $headers
     * @param \PSX\Http\StreamInterface|string $body
     */
    public function __construct($url, array $headers = array(), $body = null)
    {
        $url = $url instanceof Url ? $url : new Url((string) $url);

        parent::__construct($url, 'DELETE', $headers, $body);

        $this->setHeader('Host', $url->getHost());
    }
}

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

namespace PSX\Uri;

use InvalidArgumentException;

/**
 * Represents a URN. This class exists mostly to express in your code that
 * you expect/return a URN. Also the value must have "urn" as scheme else an
 * exception is thrown
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc2141.txt
 */
class Urn extends Uri
{
    protected $nid;
    protected $nss;

    /**
     * Returns the NID (Namespace Identifier)
     *
     * @return string
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * Returns the NSS (Namespace Specific String)
     *
     * @return string
     */
    public function getNss()
    {
        return $this->nss;
    }

    protected function parse($urn)
    {
        // URNs are case insensitive
        $urn = strtolower((string) $urn);

        parent::parse($urn);

        // must have an urn scheme and path part
        if ($this->scheme != 'urn' || empty($this->path)) {
            throw new InvalidArgumentException('Invalid urn syntax');
        }

        // parse
        $this->nid = strstr($this->path, ':', true);
        $this->nss = substr(strstr($this->path, ':'), 1);
    }

    protected function parseAuthority($authority)
    {
    }

    protected function parseParameters($query)
    {
    }
}

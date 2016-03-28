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

namespace PSX\Oauth;

use PSX\Oauth\Consumer;

/**
 * SignatureAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SignatureAbstract
{
    /**
     * Creates a signature from the base string with the consumer secret
     * as key. If the token secret is avialable it is append to the key.
     * Returns the base64 encoded signature
     *
     * @see http://oauth.net/core/1.0a#rfc.section.9
     * @param string $baseString
     * @param string $consumerSecret
     * @param string $tokenSecret
     * @return string
     */
    abstract public function build($baseString, $consumerSecret, $tokenSecret = '');

    /**
     * Compares whether the $signature is valid by creating a new signature
     * and comparing them with $signature
     *
     * @param string $baseString
     * @param string $consumerSecret
     * @param string $tokenSecret
     * @param string $signature
     * @return boolean
     */
    public function verify($baseString, $consumerSecret, $tokenSecret = '', $signature)
    {
        $lft = Consumer::urlDecode($signature);
        $rgt = Consumer::urlDecode($this->build($baseString, $consumerSecret, $tokenSecret));

        return strcasecmp($lft, $rgt) == 0;
    }
}

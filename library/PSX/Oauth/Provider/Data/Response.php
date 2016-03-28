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

namespace PSX\Oauth\Provider\Data;

use PSX\Data\Record;

/**
 * Response
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Response extends Record
{
    public function setToken($token)
    {
        $this->setProperty('oauth_token', $token);
    }

    public function getToken()
    {
        return $this->getProperty('oauth_token');
    }

    public function setTokenSecret($tokenSecret)
    {
        $this->setProperty('oauth_token_secret', $tokenSecret);
    }

    public function getTokenSecret()
    {
        return $this->getProperty('oauth_token_secret');
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            if (!in_array($key, ['oauth_token', 'oauth_token_secret'])) {
                $this->setProperty($key, $params);
            }
        }
    }

    public function getParams()
    {
        $props  = $this->getProperties();
        $result = [];
        foreach ($props as $key => $value) {
            if (!in_array($key, ['oauth_token', 'oauth_token_secret'])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function addParam($key, $value)
    {
        if (!in_array($key, ['oauth_token', 'oauth_token_secret'])) {
            $this->setProperty($key, $value);
        }
    }
}

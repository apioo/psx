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

namespace PSX\Oauth2\Authorization;

use PSX\Framework\Base;
use PSX\Oauth2\AuthorizationAbstract;

/**
 * PasswordCredentials
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PasswordCredentials extends AuthorizationAbstract
{
    /**
     * @param string $username
     * @param string $password
     * @param string $scope
     * @return \PSX\Oauth2\AccessToken
     */
    public function getAccessToken($username, $password, $scope = null)
    {
        // request data
        $data = array(
            'grant_type' => 'password',
            'username'   => $username,
            'password'   => $password,
        );

        if (isset($scope)) {
            $data['scope'] = $scope;
        }

        // authentication
        $header = array(
            'Accept'     => 'application/json',
            'User-Agent' => __CLASS__ . ' ' . Base::VERSION,
        );

        if ($this->type == self::AUTH_BASIC) {
            $header['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
        }

        if ($this->type == self::AUTH_POST) {
            $data['client_id']     = $this->clientId;
            $data['client_secret'] = $this->clientSecret;
        }

        // send request
        return $this->request($header, $data);
    }
}

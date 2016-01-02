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

namespace PSX\Oauth2\Provider\GrantType;

use PSX\Oauth2\Authorization\Exception\InvalidRequestException;
use PSX\Oauth2\Provider\Credentials;
use PSX\Oauth2\Provider\GrantTypeInterface;

/**
 * ClientCredentialsAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ClientCredentialsAbstract implements GrantTypeInterface
{
    public function getType()
    {
        return self::TYPE_CLIENT_CREDENTIALS;
    }

    public function generateAccessToken(Credentials $credentials = null, array $parameters)
    {
        if ($credentials === null) {
            throw new InvalidRequestException('Credentials not available');
        }

        $scope = isset($parameters['scope']) ? $parameters['scope'] : null;

        return $this->generate($credentials, $scope);
    }

    abstract protected function generate(Credentials $credentials, $scope);
}

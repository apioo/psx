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

namespace PSX\Oauth2\Provider\GrantType;

use PSX\Oauth2\Authorization\Exception\InvalidRequestException;
use PSX\Oauth2\Provider\Credentials;
use PSX\Oauth2\Provider\GrantTypeInterface;

/**
 * AuthorizationCodeAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class AuthorizationCodeAbstract implements GrantTypeInterface
{
	public function getType()
	{
		return self::TYPE_AUTHORIZATION_CODE;
	}

	public function generateAccessToken(Credentials $credentials = null, array $parameters)
	{
		if($credentials === null)
		{
			throw new InvalidRequestException('Credentials not available');
		}

		$code        = isset($parameters['code']) ? $parameters['code'] : null;
		$redirectUri = isset($parameters['redirect_uri']) ? $parameters['redirect_uri'] : null;
		$clientId    = isset($parameters['client_id']) ? $parameters['client_id'] : null;

		return $this->generate($credentials, $code, $redirectUri, $clientId);
	}

	abstract protected function generate(Credentials $credentials, $code, $redirectUri, $clientId);
}

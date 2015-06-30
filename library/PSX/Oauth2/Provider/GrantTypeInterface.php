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

namespace PSX\Oauth2\Provider;

/**
 * GrantTypeInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface GrantTypeInterface
{
	const TYPE_AUTHORIZATION_CODE = 'authorization_code';
	const TYPE_CLIENT_CREDENTIALS = 'client_credentials';
	const TYPE_IMPLICIT           = 'implicit';
	const TYPE_PASSWORD           = 'password';
	const TYPE_REFRESH_TOKEN      = 'refresh_token';

	/**
	 * Returns the name of this grant type
	 *
	 * @return string
	 */
	public function getType();

	/**
	 * Returns an access token based on the credentials and request parameters.
	 * In some grant types the credentials can be null
	 *
	 * @param \PSX\Oauth2\Provider\Credentials $credentials
	 * @param array $parameters
	 * @return \PSX\Oauth2\AccessToken
	 */
	public function generateAccessToken(Credentials $credentials = null, array $parameters);
}

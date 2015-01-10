<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Oauth2\Provider;

/**
 * GrantTypeInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
	 * @param PSX\Oauth2\Provider\Credentials $credentials
	 * @param array $parameters
	 * @return PSX\Oauth2\AccessToken
	 */
	public function generateAccessToken(Credentials $credentials = null, array $parameters);
}

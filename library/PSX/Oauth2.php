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

namespace PSX;

use PSX\Oauth2\AccessToken;
use PSX\Oauth2\TokenAbstract;

/**
 * Oauth2 client implementation. Here an example howto access an oauth2
 * protected api
 * <code>
 * // redirect the customer to the auth url of the provider
 * AuthorizationCode::redirect('[auth_url]', '[client_id]', '[redirect_url]');
 *
 * // if the customer returns get an access token
 * $http = new Http();
 * $code = new AuthorizationCode($http, new Url('[token_url]'));
 * $code->setClientPassword('[client_id]', '[client_secret]', AuthorizationCode::AUTH_POST);
 * 
 * $accessToken = $code->getAccessToken('[redirect_url]');
 *
 * // if we have an access token we can request the api
 * $oauth    = new Oauth2($http);
 * $header   = array(
 * 	'Authorization' => $oauth->getAuthorizationHeader($accessToken)
 * );
 * $request  = new GetRequest('[api_url]', $header);
 * $response = $http->request($request);
 *
 * if($response->getStatusCode() == 200)
 * {
 * 	// request worked
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc5849
 */
class Oauth2
{
	/**
	 * If you have received an access token you can use this method to get the
	 * authorization header. You can add the header to an http request to make
	 * an valid oauth2 request i.e.
	 * <code>
	 * $header = array(
	 * 	'Authorization: ' . $oauth->getAuthorizationHeader(...),
	 * );
	 * </code>
	 *
	 * @param PSX\Oauth2\AccessToken $accessToken
	 * @return string
	 */
	public function getAuthorizationHeader(AccessToken $accessToken)
	{
		return TokenAbstract::factory($accessToken)->getHeader();
	}
}


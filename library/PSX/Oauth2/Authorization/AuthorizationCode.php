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

namespace PSX\Oauth2\Authorization;

use PSX\Base;
use PSX\Http\Exception as StatusCode;
use PSX\Oauth2\AuthorizationAbstract;
use PSX\Url;

/**
 * AuthorizationCode
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AuthorizationCode extends AuthorizationAbstract
{
	public function getAccessToken($code, $redirectUri = null)
	{
		// request data
		$data = array(
			'grant_type' => 'authorization_code',
			'code'       => $code,
		);

		if(isset($redirectUri))
		{
			$data['redirect_uri'] = $redirectUri;
		}

		// authentication
		$header = array(
			'Accept'     => 'application/json',
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION,
		);

		if($this->type == self::AUTH_BASIC)
		{
			$header['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
		}

		if($this->type == self::AUTH_POST)
		{
			$data['client_id']     = $this->clientId;
			$data['client_secret'] = $this->clientSecret;
		}

		// send request
		return $this->request($header, $data);
	}

	/**
	 * Helper method to start the flow by redirecting the user to the 
	 * authentication server. The getAccessToken method must be used when the 
	 * server redirects the user back to the redirect uri
	 *
	 * @param PSX\Url $url
	 * @param string $clientId
	 * @param string $redirectUri
	 * @param string $scope
	 * @param string $state
	 */
	public static function redirect(Url $url, $clientId, $redirectUri = null, $scope = null, $state = null)
	{
		$parameters = $url->getParameters();
		$parameters['response_type'] = 'code';
		$parameters['client_id']     = $clientId;

		if(isset($redirectUri))
		{
			$parameters['redirect_uri'] = $redirectUri;
		}

		if(isset($scope))
		{
			$parameters['scope'] = $scope;
		}

		if(isset($state))
		{
			$parameters['state'] = $state;
		}

		$url->setScheme('https');
		$url->setParameters($parameters);

		throw new StatusCode\TemporaryRedirectException($url->toString());
	}
}

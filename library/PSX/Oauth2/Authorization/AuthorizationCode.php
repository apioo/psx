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

namespace PSX\Oauth2\Authorization;

use PSX\Base;
use PSX\Http\Exception as StatusCode;
use PSX\Oauth2\AuthorizationAbstract;
use PSX\Url;

/**
 * AuthorizationCode
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\OpenId\Connect;

use Closure;
use PSX\Base;
use PSX\Data\Record\Store;
use PSX\Data\Record\StoreInterface;
use PSX\Exception;
use PSX\Json;
use PSX\Json\WebToken;
use PSX\Oauth2\Authorization\AuthorizationCode;
use PSX\Oauth2\AuthorizationAbstract;
use PSX\Http;

/**
 * BasicClient
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BasicClient
{
	protected $http;
	protected $creds;

	public function __construct(Http $http, Credentials $creds, StoreInterface $store = null)
	{
		$this->http  = $http;
		$this->creds = $creds;
		$this->store = $store !== null ? $store : new Store\Session();
	}

	public function redirect($redirectUri, $scope)
	{
		$params = new Parameters();
		$params->setScope($scope);
		$params->setRedirectUri($redirectUri);

		$this->prepareAuthorizationRequest($params);
	}

	public function prepareAuthorizationRequest(Parameters $params)
	{
		$state = md5(uniqid());

		$params->setResponseType('code');
		$params->setClientId($this->creds->getClientId());
		$params->setState($state);

		if($params->hasFields('response_type', 'client_id', 'scope', 'redirect_uri'))
		{
			$this->store->save('openid_connect_request', $params);

			$url = clone $this->creds->getAuthorizeUrl();
			$url->setScheme('https');
			$url->addParams($params->getData());

			header('Location: ' . $url->getUrl(), true, 302);
			exit;
		}
		else
		{
			throw new Exception('Missing parameters');
		}
	}

	public function callback($code, $state, Closure $callback)
	{
		$params = $this->store->load('openid_connect_request');

		if(empty($params))
		{
			throw new Exception('Request was not initialized');
		}

		if(empty($state))
		{
			throw new Exception('State parameter not set');
		}

		if($params->getState() != $state)
		{
			throw new Exception('Invalid state');
		}

		$auth = new AuthorizationCode($this->http, $this->creds->getAccessTokenUrl());
		$auth->setClientPassword($this->creds->getClientId(), $this->creds->getClientSecret(), AuthorizationAbstract::AUTH_POST);
		$auth->setAccessTokenClass('PSX\OpenId\Connect\AccessToken');

		$token    = $auth->getAccessToken($code, $params->getRedirectUri());
		$webToken = $token->getIdToken();

		if($webToken instanceof WebToken)
		{
			$claim = Json::decode($webToken->getPayload());

			$callback($claim);
		}
		else
		{
			throw new Exception('No id token given');
		}
	}
}

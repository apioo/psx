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

namespace PSX\Oauth2\Provider;

use PSX\Controller\ApiAbstract;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Dispatch\RedirectException;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\Authorization\Exception\InvalidRequestException;
use PSX\Oauth2\Authorization\Exception\UnauthorizedClientException;
use PSX\Url;

/**
 * AuthorizationAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class AuthorizationAbstract extends ApiAbstract
{
	/**
	 * @Inject oauth2_grant_type_factory
	 * @var PSX\Oauth2\Provider\GrantTypeFactory
	 */
	protected $grantTypeFactory;

	public function onGet()
	{
		$this->doHandle();
	}

	public function onPost()
	{
		$this->doHandle();
	}

	protected function doHandle()
	{
		$responseType = $this->request->getUrl()->getParam('response_type');
		$clientId     = $this->request->getUrl()->getParam('client_id');
		$redirectUri  = $this->request->getUrl()->getParam('redirect_uri');
		$scope        = $this->request->getUrl()->getParam('scope');
		$state        = $this->request->getUrl()->getParam('state');

		try
		{
			$request = new AccessRequest($clientId, $redirectUri, $scope, $state);

			if(empty($responseType) || empty($clientId) || empty($state))
			{
				throw new InvalidRequestException('Missing parameters');
			}

			if(!empty($redirectUri))
			{
				$redirectUri = new Url($redirectUri);
			}
			else
			{
				$redirectUri = null;
			}

			if(!$this->hasGrant($request))
			{
				throw new UnauthorizedClientException('Client is not authenticated');
			}

			switch($responseType)
			{
				case 'code':
					$this->handleCode($request);
					break;

				case 'token':
					$this->handleToken($request);
					break;

				default:
					throw new UnsupportedResponseTypeException('Invalid response type');
					break;
			}
		}
		catch(ErrorExceptionAbstract $e)
		{
			$redirectUri = $this->getRedirectUri($request);

			if($redirectUri instanceof Url)
			{
				$redirectUri->setParam('error', $e->getType());
				$redirectUri->setParam('error_description', $e->getMessage());

				$this->redirect($redirectUri->getUrl());
			}
			else
			{
				throw $e;
			}
		}
		catch(RedirectException $e)
		{
			throw $e;
		}
	}

	protected function handleCode(AccessRequest $request)
	{
		$url = $this->getRedirectUri($request);

		if($url instanceof Url)
		{
			$url->setParam('code', $this->generateCode($request));

			if($request->hasState())
			{
				$url->setParam('state', $request->getState());
			}

			$this->redirect($url->getUrl());
		}
		else
		{
			throw new ServerErrorException('No redirect uri available');
		}
	}

	protected function handleToken(AccessRequest $request)
	{
		$url = $this->getRedirectUri($request);

		if($url instanceof Url)
		{
			// we must create an access token and append it to the redirect_uri
			// fragment or display an redirect form
			$accessToken = $this->grantTypeFactory->get(GrantTypeInterface::TYPE_IMPLICIT)->generateAccessToken(null, array(
				'scope' => $request->getScope()
			));

			$fields = array(
				'access_token' => $accessToken->getAccessToken(),
				'token_type'   => $accessToken->getTokenType(),
			);

			if($request->hasState())
			{
				$fields['state'] = $request->getState();
			}

			$url->setFragment(http_build_query($fields, '', '&'));

			$this->redirect($url->getUrl());
		}
		else
		{
			throw new ServerErrorException('No redirect uri available');
		}
	}

	protected function getRedirectUri(AccessRequest $request)
	{
		if($request->hasRedirectUri())
		{
			return $request->getRedirectUri();
		}
		else
		{
			return $this->getCallback($request->getClientId());
		}
	}

	/**
	 * This method is called if no redirect_uri was set you can overwrite this 
	 * method if its possible to get an callback from another source
	 *
	 * @return PSX\Url
	 */
	protected function getCallback($clientId)
	{
		return null;
	}

	/**
	 * Returns whether the user has authorized the client_id. This method must
	 * redirect the user to an login form and display an form where the user can
	 * grant the authorization request. If the request was approved
	 *
	 * @return boolean
	 */
	abstract protected function hasGrant(AccessRequest $request);

	/**
	 * Generates an authorization code which is assigned to the request
	 *
	 * @return string
	 */
	abstract protected function generateCode(AccessRequest $request);
}

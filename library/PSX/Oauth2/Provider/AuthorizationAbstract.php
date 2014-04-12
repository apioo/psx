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

namespace PSX\Oauth2;

use PSX\Controller\ApiAbstract;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * AuthorizationAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AuthorizationAbstract extends ApiAbstract
{
	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doGet()
	{
		$this->doHandle();
	}

	/**
	 * @httpMethod POST
	 * @path /
	 */
	public function doPost()
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
			if(empty($responseType) || empty($clientId) || empty($state))
			{
				throw new InvalidRequestException('Missing parameters');
			}

			$request = new AccessRequest($clientId, $redirectUri, $scope, $state);

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
		catch(InvalidRequestException $e)
		{
			$redirectUri->setParam('error', 'invalid_request');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
		}
		catch(UnauthorizedClientException $e)
		{
			$redirectUri->setParam('error', 'unauthorized_client');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
		}
		catch(AccessDeniedException $e)
		{
			$redirectUri->setParam('error', 'access_denied');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
		}
		catch(UnsupportedResponseTypeException $e)
		{
			$redirectUri->setParam('error', 'unsupported_response_type');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
		}
		catch(InvalidScopeException $e)
		{
			$redirectUri->setParam('error', 'invalid_scope');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
		}
		catch(TemporarilyEnavailableException $e)
		{
			$redirectUri->setParam('error', 'temporarily_unavailable');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
		}
		catch(RedirectException $e)
		{
			throw $e;
		}
		catch(\Exception $e)
		{
			$redirectUri->setParam('error', 'server_error');
			$redirectUri->setParam('error_description', $e->getMessage());

			$this->redirect($redirectUri->getUrl(), 302);
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

			$this->redirect($url->getUrl(), 302);
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
			$grantTypeFactory = $this->getOauth2GrantTypeFactory();
			$accessToken      = $grantTypeFactory->get(GrantTypeInterface::TYPE_IMPLICIT)->generateAccessToken(null, array(
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

			$this->redirect($url->getUrl(), 302);
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

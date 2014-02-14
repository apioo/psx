<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oauth2\Authorization\AuthorizationCode;

use PSX\Controller\ApiAbstract;
use PSX\Exception;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\AuthorizationAbstract;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CallbackAbstract extends ApiAbstract
{
	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doCallback()
	{
		try
		{
			$error = $this->request->getUrl()->getParam('error');

			// error detection
			if(!empty($error))
			{
				AuthorizationAbstract::throwErrorException($this->request->getUrl()->getParams());
			}

			$code  = $this->request->getUrl()->getParam('code');
			$state = $this->request->getUrl()->getParam('state');

			if(empty($code))
			{
				throw new Exception('Code not available');
			}

			$redirectUri = '';

			// get access token
			$accessToken = $this->getAuthorizationCode($code, $state)->getAccessToken($code, $redirectUri);

			$this->onAccessToken($accessToken);
		}
		catch(\Exception $e)
		{
			$this->onError($e);
		}
	}

	/**
	 * Should return the authorization code object containing the endpoint url
	 * and the client id and secret
	 *
	 * @param string $code
	 * @param string $state
	 * @return PSX\Oauth2\Authorization\AuthorizationCode
	 */
	abstract protected function getAuthorizationCode($code, $state);

	/**
	 * Is called if we have obtained an access token from the authorization 
	 * server
	 *
	 * @param PSX\Oauth2\AccessToken $accessToken
	 */
	abstract protected function onAccessToken(AccessToken $accessToken);

	/**
	 * Is called if the client was redirected with an GET error parameter
	 *
	 * @param Exception $e
	 */
	abstract protected function onError(\Exception $e);
}

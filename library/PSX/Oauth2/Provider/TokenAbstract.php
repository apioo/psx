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

/**
 * TokenAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TokenAbstract extends ApiAbstract
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
		$grantType   = $this->request->getUrl()->getParam('grant_type');
		$scope       = $this->request->getUrl()->getParam('scope');
		$credentials = null;

		$auth  = $this->request->getHeader('Authorization');
		$parts = explode(' ', $auth, 2);
		$type  = isset($parts[0]) ? $parts[0] : null;
		$data  = isset($parts[1]) ? $parts[1] : null;

		if($type == 'Basic' && !empty($data))
		{
			$data         = explode(':', base64_decode($data), 2);
			$clientId     = isset($data[0]) ? $data[0] : null;
			$clientSecret = isset($data[1]) ? $data[1] : null;
			$credentials  = new Credentials($clientId, $clientSecret);
		}

		try
		{
			// we get the grant type factory from the DI container the factory
			// contains the available grant types. If the service does not exist
			// an exception gets thrown
			$grantTypeFactory = $this->getOauth2GrantTypeFactory();
			$accessToken      = $grantTypeFactory->get($grantType)->generateAccessToken($credentials, $parameters);

			$this->setResponse($accessToken);
		}
		catch(\Exception $e)
		{
			$this->response->setStatusCode(400);
		}
	}
}

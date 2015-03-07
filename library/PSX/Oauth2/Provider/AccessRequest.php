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

use PSX\Url;

/**
 * AccessRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AccessRequest
{
	protected $clientId;
	protected $redirectUri;
	protected $scope;
	protected $state;

	public function __construct($clientId, $redirectUri = null, $scope = null, $state = null)
	{
		$this->clientId = $clientId;
		$this->scope    = $scope;
		$this->state    = $state;

		$this->setRedirectUri($redirectUri);
	}

	public function setClientId($clientId)
	{
		$this->clientId = $clientId;
	}
	
	public function getClientId()
	{
		return $this->clientId;
	}

	public function setRedirectUri($redirectUri)
	{
		if(!empty($redirectUri))
		{
			$this->redirectUri = new Url($redirectUri);
		}
		else
		{
			$this->redirectUri = null;
		}
	}
	
	public function getRedirectUri()
	{
		return $this->redirectUri;
	}

	public function hasRedirectUri()
	{
		return $this->redirectUri instanceof Url;
	}

	public function setScope($scope)
	{
		$this->scope = $scope;
	}
	
	public function getScope()
	{
		return $this->scope;
	}

	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function getState()
	{
		return $this->state;
	}

	public function hasState()
	{
		return !empty($this->state);
	}
}

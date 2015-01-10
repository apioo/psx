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

use PSX\Url;

/**
 * AccessRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

<?php
/*
 *  $Id: AccessToken.php 496 2012-06-02 18:41:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Oauth2_AccessToken
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Oauth2
 * @version    $Revision: 496 $
 */
class PSX_Oauth2_AccessToken extends PSX_Data_RecordAbstract
{
	public $accessToken;
	public $tokenType;
	public $expiresIn;
	public $refreshToken;
	public $scope;

	public function getName()
	{
		return 'token';
	}

	public function getFields()
	{
		return array(

			'access_token'  => $this->accessToken,
			'token_type'    => $this->tokenType,
			'expires'       => $this->expiresIn, // facebook specific
			'expires_in'    => $this->expiresIn,
			'refresh_token' => $this->refreshToken,
			'scope'         => $this->scope,

		);
	}

	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;
	}

	public function getAccessToken()
	{
		return $this->accessToken;
	}

	public function setTokenType($tokenType)
	{
		$this->tokenType = $tokenType;
	}

	public function getTokenType()
	{
		return $this->tokenType;
	}

	public function setExpires($expiresIn)
	{
		$this->expiresIn = (integer) $expiresIn;
	}

	public function setExpiresIn($expiresIn)
	{
		$this->expiresIn = (integer) $expiresIn;
	}

	public function getExpiresIn()
	{
		return $this->expiresIn;
	}

	public function setRefreshToken($refreshToken)
	{
		$this->refreshToken = $refreshToken;
	}

	public function getRefreshToken()
	{
		return $this->refreshToken;
	}

	public function setScope($scope)
	{
		$this->scope = $scope;
	}

	public function getScope()
	{
		return $this->scope;
	}
}


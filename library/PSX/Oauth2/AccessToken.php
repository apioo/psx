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

namespace PSX\Oauth2;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * AccessToken
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AccessToken extends RecordAbstract
{
	protected $accessToken;
	protected $tokenType;
	protected $expiresIn;
	protected $refreshToken;
	protected $scope;

	public function getRecordInfo()
	{
		return new RecordInfo('token', array(
			'access_token'  => $this->accessToken,
			'token_type'    => $this->tokenType,
			'expires_in'    => $this->expiresIn,
			'refresh_token' => $this->refreshToken,
			'scope'         => $this->scope,
		));
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
		$this->expiresIn = (int) $expiresIn;
	}

	public function setExpiresIn($expiresIn)
	{
		$this->expiresIn = (int) $expiresIn;
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


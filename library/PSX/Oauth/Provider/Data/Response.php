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

namespace PSX\Oauth\Provider\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Data\ReaderResult;
use PSX\Data\ReaderInterface;
use PSX\Data\NotSupportedException;

/**
 * Response
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Response extends RecordAbstract
{
	protected $token;
	protected $tokenSecret;
	protected $params = array();

	public function getRecordInfo()
	{
		return new RecordInfo('response', array(
			'oauth_token'        => $this->token,
			'oauth_token_secret' => $this->tokenSecret,
		) + $this->params);
	}

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setTokenSecret($tokenSecret)
	{
		$this->tokenSecret = $tokenSecret;
	}

	public function getTokenSecret()
	{
		return $this->tokenSecret;
	}

	public function setParams(array $params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function addParam($key, $value)
	{
		$this->params[$key] = $value;
	}
}


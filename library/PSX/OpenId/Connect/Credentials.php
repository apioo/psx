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

namespace PSX\OpenId\Connect;

use PSX\Url;

/**
 * Credentials
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Credentials
{
	protected $authorizeUrl;
	protected $accessTokenUrl;
	protected $clientId;
	protected $clientSecret;

	public function __construct(Url $authorizeUrl, Url $accessTokenUrl, $clientId, $clientSecret)
	{
		$this->authorizeUrl   = $authorizeUrl;
		$this->accessTokenUrl = $accessTokenUrl;
		$this->clientId       = $clientId;
		$this->clientSecret   = $clientSecret;
	}

	public function getAuthorizeUrl()
	{
		return $this->authorizeUrl;
	}

	public function getAccessTokenUrl()
	{
		return $this->accessTokenUrl;
	}

	public function getClientId()
	{
		return $this->clientId;
	}

	public function getClientSecret()
	{
		return $this->clientSecret;
	}
}

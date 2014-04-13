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

namespace PSX\Oauth2\Provider\GrantType;

use PSX\Oauth2\Provider\Credentials;
use PSX\Oauth2\Provider\GrantTypeInterface;
use PSX\Oauth2\Authorization\Exception\InvalidRequestException;

/**
 * RefreshTokenAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class RefreshTokenAbstract implements GrantTypeInterface
{
	public function getType()
	{
		return self::TYPE_REFRESH_TOKEN;
	}

	public function generateAccessToken(Credentials $credentials = null, array $parameters)
	{
		if($credentials === null)
		{
			throw new InvalidRequestException('Credentials not available');
		}

		$refreshToken = isset($parameters['refresh_token']) ? $parameters['refresh_token'] : null;
		$scope        = isset($parameters['scope']) ? $parameters['scope'] : null;

		return $this->generate($credentials, $refreshToken, $scope);
	}

	abstract protected function generate(Credentials $credentials, $refreshToken, $scope);
}

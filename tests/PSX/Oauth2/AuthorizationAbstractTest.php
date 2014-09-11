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

use PSX\Exception;

/**
 * AuthorizationAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AuthorizationAbstractTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException PSX\Oauth2\Authorization\Exception\InvalidRequestException
	 */
	public function testNormalErrorException()
	{
		AuthorizationAbstract::throwErrorException(array(
			'error' => 'invalid_request',
			'error_description' => 'Foobar',
			'error_uri' => 'http://foo.bar'
		));
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testEmptyErrorException()
	{
		AuthorizationAbstract::throwErrorException('');
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testUnknownErrorException()
	{
		AuthorizationAbstract::throwErrorException(array(
			'error' => 'foobar',
		));
	}

	/**
	 *
	 * @expectedException PSX\Oauth2\Authorization\Exception\InvalidRequestException
	 */
	public function testFacebookErrorException()
	{
		AuthorizationAbstract::throwErrorException(array(
			'error' => array(
				'message' => 'Message describing the error',
				'type' => 'OAuthException',
				'code' => 190,
				'error_subcode' => 460
			)
		));
	}
}


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

namespace PSX\Oauth2\Authorization\AuthorizationCode;

use PSX\Http;
use PSX\Http\Handler\Callback;
use PSX\Http\GetRequest;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth;
use PSX\OauthTest;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * CallbackAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CallbackAbstractTest extends ControllerTestCase
{
	public function testCallback()
	{
		$url      = new Url('http://foo.com/cb?code=SplxlOBeZQQYbYS6WxSbIA&state=xyz');
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new GetRequest($url, array());
		$response = new Response();
		$response->setBody($body);

		$this->loadController($request, $response);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('SUCCESS', (string) $response->getBody());
	}

	public function testErrorCallback()
	{
		$errors = array(
			'invalid_request'           => 'PSX\Oauth2\Authorization\Exception\InvalidRequestException',
			'unauthorized_client'       => 'PSX\Oauth2\Authorization\Exception\UnauthorizedClientException',
			'access_denied'             => 'PSX\Oauth2\Authorization\Exception\AccessDeniedException',
			'unsupported_response_type' => 'PSX\Oauth2\Authorization\Exception\UnsupportedResponseTypeException',
			'invalid_scope'             => 'PSX\Oauth2\Authorization\Exception\InvalidScopeException',
			'server_error'              => 'PSX\Oauth2\Authorization\Exception\ServerErrorException',
			'temporarily_unavailable'   => 'PSX\Oauth2\Authorization\Exception\TemporarilyUnavailableException',
		);

		foreach($errors as $error => $exceptionType)
		{
			$url      = new Url('http://foo.com/cb?error=' . $error . '&error_description=foobar');
			$body     = new TempStream(fopen('php://memory', 'r+'));
			$request  = new GetRequest($url, array());
			$response = new Response();
			$response->setBody($body);

			$this->loadController($request, $response);

			$this->assertEquals(500, $response->getStatusCode());
			$this->assertEquals($exceptionType, (string) $response->getBody());
		}
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/cb', 'PSX\Oauth2\Authorization\AuthorizationCode\TestCallbackAbstract'],
		);
	}
}

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

namespace PSX\Oauth2\Authorization\AuthorizationCode;

use PSX\Http;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * CallbackAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CallbackAbstractTest extends ControllerTestCase
{
	public function testCallback()
	{
		$response = $this->sendRequest('http://foo.com/cb?code=SplxlOBeZQQYbYS6WxSbIA&state=xyz', 'GET');

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
			$response = $this->sendRequest('http://foo.com/cb?error=' . $error . '&error_description=foobar', 'GET');

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

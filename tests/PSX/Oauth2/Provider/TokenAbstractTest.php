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

use PSX\Http\PostRequest;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth2\Provider\GrantType\TestAuthorizationCode;
use PSX\Oauth2\Provider\GrantType\TestClientCredentials;
use PSX\Oauth2\Provider\GrantType\TestImplicit;
use PSX\Oauth2\Provider\GrantType\TestPassword;
use PSX\Oauth2\Provider\GrantType\TestRefreshToken;
use PSX\Test\ControllerTestCase;
use PSX\Test\Environment;
use PSX\Url;

/**
 * TokenAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TokenAbstractTest extends ControllerTestCase
{
	protected function setUp()
	{
		parent::setUp();

		$grantTypeFactory = new GrantTypeFactory();
		$grantTypeFactory->add(new TestAuthorizationCode());
		$grantTypeFactory->add(new TestClientCredentials());
		$grantTypeFactory->add(new TestImplicit());
		$grantTypeFactory->add(new TestPassword());
		$grantTypeFactory->add(new TestRefreshToken());

		Environment::getContainer()->set('oauth2_grant_type_factory', $grantTypeFactory);
	}

	public function testAuthorizationCodeGrant()
	{
		$response = $this->callEndpoint('foo', 'bar', array(
			'grant_type'   => 'authorization_code',
			'code'         => 'SplxlOBeZQQYbYS6WxSbIA',
			'redirect_uri' => 'https://client.example.com/cb',
		));

		$expect = <<<JSON
{
	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	"token_type":"example",
	"expires_in":3600,
	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

		$body = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testClientCredentialsGrant()
	{
		$response = $this->callEndpoint('foo', 'bar', array(
			'grant_type' => 'client_credentials',
		));

		$expect = <<<JSON
{
	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	"token_type":"example",
	"expires_in":3600,
	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

		$body = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testPasswordGrant()
	{
		$response = $this->callEndpoint('foo', 'bar', array(
			'grant_type' => 'password',
			'username'   => 'johndoe',
			'password'   => 'A3ddj3w',
		));

		$expect = <<<JSON
{
	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	"token_type":"example",
	"expires_in":3600,
	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

		$body = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testRefreshTokenGrant()
	{
		$response = $this->callEndpoint('foo', 'bar', array(
			'grant_type'    => 'refresh_token',
			'refresh_token' => 'tGzv3JOkF0XG5Qx2TlKWIA',
		));

		$expect = <<<JSON
{
	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	"token_type":"example",
	"expires_in":3600,
	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
}
JSON;

		$body = (string) $response->getBody();

		$this->assertEquals(200, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	public function testInvalidGrant()
	{
		$response = $this->callEndpoint('foo', 'bar', array(
			'grant_type' => 'foo',
		));

		$expect = <<<JSON
{
	"error":"server_error",
	"error_description":"Invalid grant type"
}
JSON;

		$body = (string) $response->getBody();

		$this->assertEquals(400, $response->getStatusCode(), $body);
		$this->assertJsonStringEqualsJsonString($expect, $body, $body);
	}

	protected function callEndpoint($clientId, $clientSecret, array $params)
	{
		$url      = new Url('http://127.0.0.1/token');
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new PostRequest($url, array('Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)), $params);
		$response = new Response();
		$response->setBody($body);

		$this->loadController($request, $response);

		return $response;
	}

	protected function getPaths()
	{
		return array(
			[['POST'], '/token', 'PSX\Oauth2\Provider\TestTokenAbstract'],
		);
	}
}

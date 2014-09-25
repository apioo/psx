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
use PSX\Url;

/**
 * TokenAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		getContainer()->set('oauth2_grant_type_factory', $grantTypeFactory);
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

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
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

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
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

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
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

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
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

		$this->assertEquals(400, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString($expect, (string) $response->getBody());
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

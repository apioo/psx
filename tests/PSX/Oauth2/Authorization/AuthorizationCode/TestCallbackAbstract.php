<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
use PSX\Http\Authentication;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\Oauth2\Authorization\AuthorizationCode;
use PSX\Oauth2\Authorization\ClientCredentialsTest;
use PSX\Oauth2\AccessToken;
use PSX\Url;

/**
 * TestCallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestCallbackAbstract extends CallbackAbstract
{
	/**
	 * @Inject
	 * @var PHPUnit_Framework_TestCase
	 */
	protected $testCase;

	protected function getAuthorizationCode($code, $state)
	{
		$testCase = $this->testCase;
		$http = new Http(new Callback(function($request) use ($testCase){

			// api request
			if($request->getUri()->getPath() == '/api')
			{
				$testCase->assertEquals('Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW', (string) $request->getHeader('Authorization'));
				$testCase->assertEquals('application/x-www-form-urlencoded', (string) $request->getHeader('Content-Type'));

				$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: application/json;charset=UTF-8
Cache-Control: no-store
Pragma: no-cache

{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
  "example_parameter":"example_value"
}
TEXT;
			}

			return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$oauth = new AuthorizationCode($http, new Url('http://127.0.0.1/api'), getContainer()->get('importer'));
		$oauth->setClientPassword(ClientCredentialsTest::CLIENT_ID, ClientCredentialsTest::CLIENT_SECRET);

		return $oauth;
	}

	protected function onAccessToken(AccessToken $accessToken)
	{
		$this->testCase->assertEquals('2YotnFZFEjr1zCsicMWpAA', $accessToken->getAccessToken());
		$this->testCase->assertEquals('example', $accessToken->getTokenType());
		$this->testCase->assertEquals(3600, $accessToken->getExpiresIn());
		$this->testCase->assertEquals('tGzv3JOkF0XG5Qx2TlKWIA', $accessToken->getRefreshToken());

		$this->response->setStatus(200);
		$this->response->getBody()->write('SUCCESS');
	}

	protected function onError(\Exception $e)
	{
		$this->response->setStatus(500);
		$this->response->getBody()->write(get_class($e));
	}
}

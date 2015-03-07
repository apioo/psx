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

namespace PSX\Oauth\Provider;

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
 * Flow which test the complete oauth stack
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FlowAbstractTest extends ControllerTestCase
{
	public function testFlow()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$oauth = new Oauth($http);

		// request token
		$response = $oauth->requestToken(new Url('http://127.0.0.1/request'), OauthTest::CONSUMER_KEY, OauthTest::CONSUMER_SECRET);

		$this->assertInstanceOf('PSX\Oauth\Provider\Data\Response', $response);
		$this->assertEquals(OauthTest::TMP_TOKEN, $response->getToken());
		$this->assertEquals(OauthTest::TMP_TOKEN_SECRET, $response->getTokenSecret());

		// authorize the user gets redirected and approves the application

		// access token
		$response = $oauth->accessToken(new Url('http://127.0.0.1/access'), OauthTest::CONSUMER_KEY, OauthTest::CONSUMER_SECRET, OauthTest::TMP_TOKEN, OauthTest::TMP_TOKEN_SECRET, OauthTest::VERIFIER);

		$this->assertInstanceOf('PSX\Oauth\Provider\Data\Response', $response);
		$this->assertEquals(OauthTest::TOKEN, $response->getToken());
		$this->assertEquals(OauthTest::TOKEN_SECRET, $response->getTokenSecret());

		// api request
		$url      = new Url('http://127.0.0.1/api');
		$auth     = $oauth->getAuthorizationHeader($url, OauthTest::CONSUMER_KEY, OauthTest::CONSUMER_SECRET, OauthTest::TOKEN, OauthTest::TOKEN_SECRET, 'HMAC-SHA1', 'GET');
		$request  = new GetRequest($url, array('Authorization' => $auth));
		$response = $http->request($request);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('SUCCESS', (string) $response->getBody());
	}

	protected function getPaths()
	{
		return array(
			[['POST'], '/request', 'PSX\Oauth\Provider\TestRequestAbstract'],
			[['POST'], '/access', 'PSX\Oauth\Provider\TestAccessAbstract'],
			[['GET'], '/api', 'PSX\Oauth\Provider\TestOauth::doIndex'],
		);
	}
}

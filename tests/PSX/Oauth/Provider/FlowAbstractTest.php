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

namespace PSX\Oauth\Provider;

use PSX\Controller\ControllerTestCase;
use PSX\Http;
use PSX\Http\Handler\Callback;
use PSX\Http\GetRequest;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth;
use PSX\OauthTest;
use PSX\Url;

/**
 * Flow which test the complete oauth stack
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
			'/request' => 'PSX\Oauth\Provider\TestRequestAbstract',
			'/access'  => 'PSX\Oauth\Provider\TestAccessAbstract',
			'/api'     => 'PSX\Oauth\Provider\TestOauth',
		);
	}
}

<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oauth2\Tests\Authorization\AuthorizationCode;

use PSX\Framework\Test\Environment;
use PSX\Http;
use PSX\Http\Handler\Callback;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseParser;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Authorization\AuthorizationCode;
use PSX\Oauth2\Authorization\AuthorizationCode\CallbackAbstract;
use PSX\Oauth2\Tests\Authorization\ClientCredentialsTest;
use PSX\Uri\Url;

/**
 * TestCallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestCallbackAbstract extends CallbackAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    protected function getAuthorizationCode($code, $state)
    {
        $testCase = $this->testCase;
        $httpClient = new Http\Client(new Callback(function (RequestInterface $request) use ($testCase) {

            // api request
            if ($request->getUri()->getPath() == '/api') {
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
            } else {
                throw new \RuntimeException('Invalid path');
            }

            return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();

        }));
        $oauth = new AuthorizationCode($httpClient, new Url('http://127.0.0.1/api'));
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

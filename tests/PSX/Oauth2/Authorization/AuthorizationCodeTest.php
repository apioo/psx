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

namespace PSX\Oauth2\Authorization;

use PSX\Http;
use PSX\Http\Exception\TemporaryRedirectException;
use PSX\Http\Handler\Callback;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseParser;
use PSX\Test\Environment;
use PSX\Url;

/**
 * AuthorizationCodeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AuthorizationCodeTest extends \PHPUnit_Framework_TestCase
{
    const CLIENT_ID     = 's6BhdRkqt3';
    const CLIENT_SECRET = 'gX1fBat3bV';

    public function testRequest()
    {
        $testCase = $this;
        $http = new Http(new Callback(function (RequestInterface $request) use ($testCase) {

            // api request
            if ($request->getUri()->getPath() == '/api') {
                $testCase->assertEquals('Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW', (string) $request->getHeader('Authorization'));
                $testCase->assertEquals('application/x-www-form-urlencoded', (string) $request->getHeader('Content-Type'));
                $testCase->assertEquals('grant_type=authorization_code&code=SplxlOBeZQQYbYS6WxSbIA', (string) $request->getBody());

                $response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: application/json;charset=UTF-8
Cache-Control: no-store
Pragma: no-cache

{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "example_parameter":"example_value"
}
TEXT;
            } else {
                throw new \RuntimeException('Invalid path');
            }

            return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();

        }));

        $oauth = new AuthorizationCode($http, new Url('http://127.0.0.1/api'), Environment::getService('importer'));
        $oauth->setClientPassword(self::CLIENT_ID, self::CLIENT_SECRET);

        $accessToken = $oauth->getAccessToken('SplxlOBeZQQYbYS6WxSbIA');

        $this->assertEquals('2YotnFZFEjr1zCsicMWpAA', $accessToken->getAccessToken());
        $this->assertEquals('example', $accessToken->getTokenType());
        $this->assertEquals(3600, $accessToken->getExpiresIn());
    }

    public function testRedirect()
    {
        try {
            AuthorizationCode::redirect(new Url('http://127.0.0.1/api'), self::CLIENT_ID, 'http://127.0.0.1/return', 'foo,bar', 'foo-state');

            $this->fail('Must throw an redirect exception');
        } catch (TemporaryRedirectException $e) {
            $this->assertEquals('https://127.0.0.1/api?response_type=code&client_id=s6BhdRkqt3&redirect_uri=http%3A%2F%2F127.0.0.1%2Freturn&scope=foo%2Cbar&state=foo-state', $e->getLocation());
        }
    }
}

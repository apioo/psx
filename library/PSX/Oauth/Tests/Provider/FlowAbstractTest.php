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

namespace PSX\Oauth\Tests\Provider;

use PSX\Http\Client as HttpClient;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth\Consumer;
use PSX\Oauth\Tests\ConsumerTest;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Uri\Url;

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
        $httpClient = new HttpClient(new Callback(function ($request) use ($testCase) {

            $body     = new TempStream(fopen('php://memory', 'r+'));
            $response = new Response();
            $response->setBody($body);

            $testCase->loadController($request, $response);

            return $response;

        }));

        $oauth = new Consumer($httpClient);

        // request token
        $response = $oauth->requestToken(new Url('http://127.0.0.1/request'), ConsumerTest::CONSUMER_KEY, ConsumerTest::CONSUMER_SECRET);

        $this->assertInstanceOf('PSX\Oauth\Provider\Data\Response', $response);
        $this->assertEquals(ConsumerTest::TMP_TOKEN, $response->getToken());
        $this->assertEquals(ConsumerTest::TMP_TOKEN_SECRET, $response->getTokenSecret());

        // authorize the user gets redirected and approves the application

        // access token
        $response = $oauth->accessToken(new Url('http://127.0.0.1/access'), ConsumerTest::CONSUMER_KEY, ConsumerTest::CONSUMER_SECRET, ConsumerTest::TMP_TOKEN, ConsumerTest::TMP_TOKEN_SECRET, ConsumerTest::VERIFIER);

        $this->assertInstanceOf('PSX\Oauth\Provider\Data\Response', $response);
        $this->assertEquals(ConsumerTest::TOKEN, $response->getToken());
        $this->assertEquals(ConsumerTest::TOKEN_SECRET, $response->getTokenSecret());

        // api request
        $url      = new Url('http://127.0.0.1/api');
        $auth     = $oauth->getAuthorizationHeader($url, ConsumerTest::CONSUMER_KEY, ConsumerTest::CONSUMER_SECRET, ConsumerTest::TOKEN, ConsumerTest::TOKEN_SECRET, 'HMAC-SHA1', 'GET');
        $request  = new GetRequest($url, array('Authorization' => $auth));
        $response = $httpClient->request($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SUCCESS', (string) $response->getBody());
    }

    protected function getPaths()
    {
        return array(
            [['POST'], '/request', 'PSX\Oauth\Tests\Provider\TestRequestAbstract'],
            [['POST'], '/access', 'PSX\Oauth\Tests\Provider\TestAccessAbstract'],
            [['GET'], '/api', 'PSX\Oauth\Tests\Provider\TestOauth::doIndex'],
        );
    }
}

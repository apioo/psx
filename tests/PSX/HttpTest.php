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

namespace PSX;

use PSX\Http\CookieStore;
use PSX\Http\GetRequest;
use PSX\Http\Handler;
use PSX\Http\ResponseParser;

/**
 * HttpTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
    public function testCookieStore()
    {
        $store = new CookieStore\Memory();
        $http  = new Http(new Handler\Callback(function ($request) {

            $response = <<<TEXT
HTTP/1.1 200 OK
Content-Encoding: gzip
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT
ETag: "815832758"
Set-Cookie: webmaker.sid=s%3Aj%3A%7B%22_csrfSecret%22%3A%22uMs5W0M2tR2ewHNiJQye7lpe%22%7D.wSMQqQeiDgatt0Smv2Nbq5g92lX04%2FmOBiiRdPZIuro; Path=/; Expires=Tue, 04 Feb 2024 18:19:45 GMT; HttpOnly; Secure
Strict-Transport-Security: max-age=15768000
Vary: Accept-Encoding
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
transfer-encoding: chunked
Connection: keep-alive

foobar
TEXT;

            return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();

        }));

        $http->setCookieStore($store);

        $request  = new GetRequest(new Url('http://localhost.com'));
        $response = $http->request($request);
        $cookies  = $store->load('localhost.com');

        $this->assertTrue(isset($cookies['webmaker.sid']));
        $this->assertEquals('webmaker.sid', $cookies['webmaker.sid']->getName());
        $this->assertEquals('s%3Aj%3A%7B%22_csrfSecret%22%3A%22uMs5W0M2tR2ewHNiJQye7lpe%22%7D.wSMQqQeiDgatt0Smv2Nbq5g92lX04%2FmOBiiRdPZIuro', $cookies['webmaker.sid']->getValue());
        $this->assertEquals(new \DateTime('Tue, 04 Feb 2024 18:19:45 GMT'), $cookies['webmaker.sid']->getExpires());
        $this->assertEquals('/', $cookies['webmaker.sid']->getPath());
        $this->assertEquals(null, $cookies['webmaker.sid']->getDomain());
        $this->assertEquals(true, $cookies['webmaker.sid']->getSecure());
        $this->assertEquals(true, $cookies['webmaker.sid']->getHttpOnly());

        // now we have stored the cookie we check whether we get it on the next
        // request
        $testCase = $this;
        $http     = new Http(new Handler\Callback(function ($request) use ($testCase) {

            $cookie = $request->getHeader('Cookie');
            $testCase->assertEquals('webmaker.sid=s%3Aj%3A%7B%22_csrfSecret%22%3A%22uMs5W0M2tR2ewHNiJQye7lpe%22%7D.wSMQqQeiDgatt0Smv2Nbq5g92lX04%2FmOBiiRdPZIuro', (string) $cookie);

            $response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

foobar
TEXT;

            return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();

        }));

        $http->setCookieStore($store);

        $request  = new GetRequest(new Url('http://localhost.com'));
        $response = $http->request($request);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRelativeUrl()
    {
        $http    = new Http();
        $request = new GetRequest(new Uri('/foo/bar'));

        $http->request($request);
    }

    public function testSetGetHandler()
    {
        $http = new Http();

        $this->assertInstanceOf('PSX\Http\Handler\Curl', $http->getHandler());

        $http->setHandler(new Handler\Socks());

        $this->assertInstanceOf('PSX\Http\Handler\Socks', $http->getHandler());
    }

    public function testSetGetCookieStore()
    {
        $http = new Http();

        $this->assertEmpty($http->getCookieStore());

        $http->setCookieStore(new CookieStore\Memory());

        $this->assertInstanceOf('PSX\Http\CookieStore\Memory', $http->getCookieStore());
    }
}

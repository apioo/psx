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

namespace PSX\Oauth\Tests;

use PSX\Http\Client as HttpClient;
use PSX\Http\Authentication;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseParser;
use PSX\Oauth\Consumer;
use PSX\Uri\Url;

/**
 * ConsumerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    const CONSUMER_KEY      = 'dpf43f3p2l4k3l03';
    const CONSUMER_SECRET   = 'kd94hf93k423kf44';

    const TMP_TOKEN         = 'hh5s93j4hdidpola';
    const TMP_TOKEN_SECRET  = 'hdhd0244k9j7ao03';
    const VERIFIER          = 'hfdp7dh39dks9884';
    const TOKEN             = 'nnch734d00sl2jdk';
    const TOKEN_SECRET      = 'pfkkdhi9sl3r4s00';

    public function testFlow()
    {
        $testCase = $this;
        $http = new HttpClient(new Callback(function (RequestInterface $request) use ($testCase) {

            // request token
            if ($request->getUri()->getPath() == '/requestToken') {
                $auth = Authentication::decodeParameters((string) $request->getHeader('Authorization'));

                $testCase->assertEquals(self::CONSUMER_KEY, $auth['oauth_consumer_key']);
                $testCase->assertEquals('HMAC-SHA1', $auth['oauth_signature_method']);
                $testCase->assertTrue(isset($auth['oauth_timestamp']));
                $testCase->assertTrue(isset($auth['oauth_nonce']));
                $testCase->assertEquals('1.0', $auth['oauth_version']);
                $testCase->assertEquals('oob', $auth['oauth_callback']);
                $testCase->assertTrue(isset($auth['oauth_signature']));

                $tmpToken       = self::TMP_TOKEN;
                $tmpTokenSecret = self::TMP_TOKEN_SECRET;

                $response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:25 GMT
Content-Type: application/x-www-form-urlencoded

oauth_token={$tmpToken}&oauth_token_secret={$tmpTokenSecret}&oauth_callback_confirmed=1
TEXT;
            }
            // access token
            elseif ($request->getUri()->getPath() == '/accessToken') {
                $auth = Authentication::decodeParameters((string) $request->getHeader('Authorization'));

                $testCase->assertEquals(self::CONSUMER_KEY, $auth['oauth_consumer_key']);
                $testCase->assertEquals(self::TMP_TOKEN, $auth['oauth_token']);
                $testCase->assertEquals('HMAC-SHA1', $auth['oauth_signature_method']);
                $testCase->assertTrue(isset($auth['oauth_timestamp']));
                $testCase->assertTrue(isset($auth['oauth_nonce']));
                $testCase->assertEquals('1.0', $auth['oauth_version']);
                $testCase->assertEquals(self::VERIFIER, $auth['oauth_verifier']);
                $testCase->assertTrue(isset($auth['oauth_signature']));

                $token       = self::TOKEN;
                $tokenSecret = self::TOKEN_SECRET;

                $response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/x-www-form-urlencoded

oauth_token={$token}&oauth_token_secret={$tokenSecret}
TEXT;
            }
            // api request
            elseif ($request->getUri()->getPath() == '/api') {
                $auth = Authentication::decodeParameters((string) $request->getHeader('Authorization'));

                $testCase->assertEquals(self::CONSUMER_KEY, $auth['oauth_consumer_key']);
                $testCase->assertEquals(self::TOKEN, $auth['oauth_token']);
                $testCase->assertEquals('HMAC-SHA1', $auth['oauth_signature_method']);
                $testCase->assertTrue(isset($auth['oauth_timestamp']));
                $testCase->assertTrue(isset($auth['oauth_nonce']));
                $testCase->assertEquals('1.0', $auth['oauth_version']);
                $testCase->assertTrue(isset($auth['oauth_signature']));

                $response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: text/html; charset=UTF-8

SUCCESS
TEXT;
            } else {
                throw new \RuntimeException('Invalid path');
            }

            return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();

        }));

        $oauth = new Consumer($http);

        // request token
        $url      = new Url('http://127.0.0.1/requestToken');
        $response = $oauth->requestToken($url, self::CONSUMER_KEY, self::CONSUMER_SECRET);

        $this->assertInstanceOf('PSX\Oauth\Provider\Data\Response', $response);
        $this->assertEquals(self::TMP_TOKEN, $response->getToken());
        $this->assertEquals(self::TMP_TOKEN_SECRET, $response->getTokenSecret());

        // if we have optained temporary credentials we can redirect the user
        // to grant access to the credentials
        // $oauth->userAuthorization($url, array('oauth_token' => $response->getToken()))

        // if the user gets redirected back we can exchange the temporary
        // credentials to an access token we also get an verifier as GET
        // parameter
        $url      = new Url('http://127.0.0.1/accessToken');
        $response = $oauth->accessToken($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TMP_TOKEN, self::TMP_TOKEN, self::VERIFIER);

        $this->assertInstanceOf('PSX\Oauth\Provider\Data\Response', $response);
        $this->assertEquals(self::TOKEN, $response->getToken());
        $this->assertEquals(self::TOKEN_SECRET, $response->getTokenSecret());

        // now we can make an request to the protected api
        $url      = new Url('http://127.0.0.1/api');
        $auth     = $oauth->getAuthorizationHeader($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET, 'HMAC-SHA1', 'GET');
        $request  = new GetRequest($url, array('Authorization' => $auth));
        $response = $http->request($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SUCCESS', (string) $response->getBody());
    }

    public function testOAuthBuildAuthString()
    {
        $data = Consumer::buildAuthString(array('fo o' => 'b~ar'));

        $this->assertEquals('fo%20o="b~ar"', $data);
    }

    public function testOAuthGetNormalizedUrl()
    {
        $url = new Url('HTTP://Example.com:80/resource?id=123');

        $this->assertEquals('http://example.com/resource', Consumer::getNormalizedUrl($url));


        $url = new Url('http://localhost:8888/amun/public/index.php/api/auth/request');

        $this->assertEquals('http://localhost:8888/amun/public/index.php/api/auth/request', Consumer::getNormalizedUrl($url));
    }

    /**
     * Tests the getNormalizedParameters function by using the values from the
     * RFC. The one "a3" value is commented because we cant have two keys with
     * the same name in an array to make it work we have to change the
     * datastructure but by now no problems occured with this issue
     *
     * @see http://tools.ietf.org/html/rfc5849#section-3.4.2
     * @see http://wiki.oauth.net/w/page/12238556/TestCases
     */
    public function testOAuthGetNormalizedParameters()
    {
        $params = Consumer::getNormalizedParameters(array(

            'b5' => '=%3D',
            //'a3' => 'a',
            'c@' => '',
            'a2' => 'r b',
            'oauth_consumer_key' => '9djdj82h48djs9d2',
            'oauth_token' => 'kkk9d7dh3k39sjv7',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '137131201',
            'oauth_nonce' => '7d8f3e4a',
            'c2' => '',
            'a3' => '2 q',

        ));

        $expect = 'a2=r%20b&a3=2%20q&b5=%3D%253D&c%40=&c2=&oauth_consumer_key=9djdj82h48djs9d2&oauth_nonce=7d8f3e4a&oauth_signature_method=HMAC-SHA1&oauth_timestamp=137131201&oauth_token=kkk9d7dh3k39sjv7';

        $this->assertEquals($expect, $params);


        $params = array(
            'name='       => array('name' => ''),
            'a=b'         => array('a' => 'b'),
            'a=b&c=d'     => array('a' => 'b', 'c' => 'd'),
            'a=x%2By'     => array('a' => 'x+y'),
            'x=a&x%21y=a' => array('x!y' => 'a', 'x' => 'a'),
        );

        foreach ($params as $expect => $param) {
            $this->assertEquals($expect, Consumer::getNormalizedParameters($param));
        }
    }

    /**
     * Tests url encoding
     *
     * @see http://wiki.oauth.net/w/page/12238556/TestCases
     */
    public function testParameterEncoding()
    {
        $values = array(
            'abcABC123' => 'abcABC123',
            '-._~'      => '-._~',
            '%'         => '%25',
            '+'         => '%2B',
            '&=*'       => '%26%3D%2A',
            "\x0A"      => '%0A',
            "\x20"      => '%20',
            //"\x80"      => '%C2%80',
        );

        foreach ($values as $k => $v) {
            $this->assertEquals($v, Consumer::urlEncode($k));
        }
    }
}

<?php
/*
 *  $Id: OauthTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_OauthTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_OauthTest extends PHPUnit_Framework_TestCase
{
	const URL_REQUEST_TOKEN = 'http://test.phpsx.org/oauth/requestToken';
	const URL_AUTH          = 'http://test.phpsx.org/oauth/auth';
	const URL_ACCESS_TOKEN  = 'http://test.phpsx.org/oauth/accessToken';
	const URL_API           = 'http://test.phpsx.org/oauth/api';

	const CONSUMER_KEY      = 'dpf43f3p2l4k3l03';
	const CONSUMER_SECRET   = 'kd94hf93k423kf44';

	const TMP_TOKEN         = 'hh5s93j4hdidpola';
	const TMP_TOKEN_SECRET  = 'hdhd0244k9j7ao03';
	const VERIFIER          = 'hfdp7dh39dks9884';
	const TOKEN             = 'nnch734d00sl2jdk';
	const TOKEN_SECRET      = 'pfkkdhi9sl3r4s00';

	private $http;
	private $oauth;

	protected function setUp()
	{
		$this->http  = new PSX_Http(new PSX_Http_Handler_Curl());
		$this->oauth = new PSX_Oauth($this->http);
	}

	protected function tearDown()
	{
	}

	public function testRequestToken()
	{
		$url = new PSX_Url(self::URL_REQUEST_TOKEN);

		$response = $this->oauth->requestToken($url, self::CONSUMER_KEY, self::CONSUMER_SECRET);

		// if we have requested too much tokens we get an error that we
		// only request 5 valid tokens but the request was correct
		if($response !== false)
		{
			$this->assertEquals(self::TMP_TOKEN, $response->getToken(), $this->http->getResponse());

			$this->assertEquals(self::TMP_TOKEN_SECRET, $response->getTokenSecret(), $this->http->getResponse());
		}
		else
		{
			$lastError = $this->oauth->getLastError();
			$lastError = !empty($lastError) ? $lastError : 'Couldnt get request token';

			throw new PSX_Exception($lastError);
		}
	}

	/**
	 * @depends testRequestToken
	 */
	public function testAccessToken()
	{
		$url = new PSX_Url(self::URL_ACCESS_TOKEN);

		$response = $this->oauth->accessToken($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TMP_TOKEN, self::TMP_TOKEN_SECRET, self::VERIFIER);

		// if we have requested too much tokens we get an error that we
		// only request 5 valid tokens but the request was correct
		if($response !== false)
		{
			$this->assertEquals(self::TOKEN, $response->getToken(), $this->http->getResponse());

			$this->assertEquals(self::TOKEN_SECRET, $response->getTokenSecret(), $this->http->getResponse());
		}
		else
		{
			$lastError = $this->oauth->getLastError();
			$lastError = !empty($lastError) ? $lastError : 'Couldnt get access token';

			throw new PSX_Exception($lastError);
		}
	}

	/**
	 * @depends testAccessToken
	 */
	public function testApiRequest()
	{
		$url = new PSX_Url(self::URL_API);

		$request = new PSX_Http_GetRequest($url, array(

			'Authorization' => $this->oauth->getAuthorizationHeader($url, self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET, 'HMAC-SHA1', 'GET'),

		));

		$response = $this->http->request($request);

		$this->assertEquals('SUCCESS', $response->getBody());
	}

	public function testOAuthBuildAuthString()
	{
		$data = PSX_Oauth::buildAuthString(array('fo o' => 'b~ar'));

		$this->assertEquals('fo%20o="b~ar"', $data);
	}

	public function testOAuthGetNormalizedUrl()
	{
		$url = new PSX_Url('HTTP://Example.com:80/resource?id=123');

		$this->assertEquals('http://example.com/resource', PSX_Oauth::getNormalizedUrl($url));


		$url = new PSX_Url('http://localhost:8888/amun/public/index.php/api/auth/request');

		$this->assertEquals('http://localhost:8888/amun/public/index.php/api/auth/request', PSX_Oauth::getNormalizedUrl($url));

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
		$params = PSX_Oauth::getNormalizedParameters(array(

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

		foreach($params as $expect => $param)
		{
			$this->assertEquals($expect, PSX_Oauth::getNormalizedParameters($param));
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

		foreach($values as $k => $v)
		{
			$this->assertEquals($v, PSX_Oauth::urlEncode($k));
		}
	}
}


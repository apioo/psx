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

namespace PSX;

use PSX\Http\Handler\Callback;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
use PSX\OpenId\Store;
use PSX\OpenId\ProviderAbstract;

/**
 * OpenIdTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OpenIdTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		parent::setUp();

		// key type OPENSSL_KEYTYPE_DH is not supported by hhvm
		if(getenv('TRAVIS_PHP_VERSION') == 'hhvm')
		{
			$this->markTestSkipped('Key type OPENSSL_KEYTYPE_DH is not supported by hhvm');
		}
	}

	protected function tearDown()
	{
	}

	public function testInitialize()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			// association endpoint
			if($request->getUrl()->getPath() == '/server')
			{
				$data = array();
				parse_str($request->getBody(), $data);

				$testCase->assertEquals('http://specs.openid.net/auth/2.0', $data['openid_ns']);
				$testCase->assertEquals('associate', $data['openid_mode']);
				$testCase->assertEquals('HMAC-SHA256', $data['openid_assoc_type']);
				$testCase->assertEquals('DH-SHA256', $data['openid_session_type']);

				$dhGen         = $data['openid_dh_gen'];
				$dhModulus     = $data['openid_dh_modulus'];
				$dhConsumerPub = $data['openid_dh_consumer_public'];
				$dhFunc        = 'SHA1';
				$secret        = ProviderAbstract::randomBytes(20);

				$res = ProviderAbstract::generateDh($dhGen, $dhModulus, $dhConsumerPub, $dhFunc, $secret);

				$testCase->assertEquals(true, isset($res['pubKey']));
				$testCase->assertEquals(true, isset($res['macKey']));

				$body = OpenId::keyValueEncode(array(
					'ns'               => 'http://specs.openid.net/auth/2.0',
					'assoc_handle'     => 'foobar',
					'session_type'     => 'DH-SHA256',
					'assoc_type'       => 'HMAC-SHA256',
					'expires_in'       => 60 * 60,
					'dh_server_public' => $res['pubKey'],
					'enc_mac_key'      => $res['macKey'],
				));

				$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

{$body}
TEXT;
			}
			// identity html base discovery
			else if($request->getUrl()->getPath() == '/identity')
			{
				$response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
Date: Sat, 04 Jan 2014 18:19:45 GMT

<html>
	<head>
		<link rel="openid.server" href="http://openid.com/server" />
		<link rel="openid.delegate" href="http://foo.com" />
	</head>
</html>
TEXT;
			}

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));
		$store  = new Store\Memory();
		$openid = new OpenId($http, 'http://localhost.com', $store);
		$openid->initialize('http://foo.com/identity', 'http://localhost.com/callback');

		// check whether the store has the association
		$assoc = $store->loadByHandle('http://openid.com/server', 'foobar');

		$this->assertEquals('foobar', $assoc->getAssocHandle());
		$this->assertEquals('HMAC-SHA256', $assoc->getAssocType());
		$this->assertEquals('DH-SHA256', $assoc->getSessionType());
		$this->assertEquals(3600, $assoc->getExpire());

		// check redirect url
		$url = $openid->getRedirectUrl();

		$this->assertEquals('http://specs.openid.net/auth/2.0', $url->getParam('openid.ns'));
		$this->assertEquals('checkid_setup', $url->getParam('openid.mode'));
		$this->assertEquals('http://localhost.com/callback', $url->getParam('openid.return_to'));
		$this->assertEquals('http://localhost.com', $url->getParam('openid.realm'));
		$this->assertEquals('http://foo.com/identity', $url->getParam('openid.claimed_id'));
		$this->assertEquals('http://foo.com', $url->getParam('openid.identity'));
		$this->assertEquals('foobar', $url->getParam('openid.assoc_handle'));

		// the user gets redirected from the openid provider to our callback now
		// we verfiy the data
		$signed = array('ns','mode','op_endpoint','return_to','response_nonce','assoc_handle');

		$data = array(
			'openid_ns'             => 'http://specs.openid.net/auth/2.0',
			'openid_mode'           => 'id_res',
			'openid_op_endpoint'    => 'http://openid.com/server',
			'openid_return_to'      => 'http://localhost.com/callback',
			'openid_response_nonce' => uniqid(),
			'openid_assoc_handle'   => $assoc->getAssocHandle(),
			'openid_signed'         => implode(',', $signed),
		);

		// generate signature
		$sig = OpenId::buildSignature(OpenId::extractParams($data), $signed, $assoc->getSecret(), $assoc->getAssocType());

		$data['openid_sig'] = $sig;

		// verify
		$result = $openid->verify($data);

		$this->assertTrue($result);
	}

	public function testOpenIDKeyValue()
	{
		$expect = array('mode' => 'error', 'error' => 'This is an example message');

		$str = 'mode:error' . "\n" . 'error:This is an example message' . "\n";

		$this->assertEquals($str, OpenId::keyValueEncode($expect));

		$this->assertEquals($expect, OpenId::keyValueDecode($str));
	}
}


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

namespace PSX\OpenId;

use PSX\Controller\ControllerTestCase;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\OpenId;
use PSX\OpenSsl;
use PSX\OpenSsl\PKey;
use PSX\Url;

/**
 * ProviderAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ProviderAbstractTest extends ControllerTestCase
{
	public function testFlow()
	{
		$consumerRequest = $this->generateConsumerRequest();

		// association request
		$data = $this->doAssociation($consumerRequest['modulus'], $consumerRequest['gen'], $consumerRequest['consumer_public']);

		// we received the encrypted secret over the wire. The client can now
		// decrypt the secret
		$assocHandle     = $data['assoc_handle'];
		$assocType       = $data['assoc_type'];
		$serverPublicKey = $data['dh_server_public'];
		$encMacKey       = $data['enc_mac_key'];

		$this->assertTrue(!empty($assocHandle));
		$this->assertTrue(!empty($assocType));
		$this->assertTrue(!empty($serverPublicKey));
		$this->assertTrue(!empty($encMacKey));

		$serverPub    = base64_decode($serverPublicKey);
		$dhSec        = OpenSsl::dhComputeKey($serverPub, $consumerRequest['pkey']);
		$sec          = OpenSsl::digest(ProviderAbstract::btwoc($dhSec), 'SHA1', true);
		$serverSecret = base64_encode($sec ^ base64_decode($data['enc_mac_key']));

		// the client has established the association we can make now an
		// checkid setup request. If the user is authenticated he gets 
		// redirected back to the relying party
		$url = $this->doCheckidSetupRequest($assocHandle);

		// later the relying party calls check authentication to verify the 
		// callback
		$this->doCheckAuthentication($url);
	}

	public function testGetExtension()
	{
		$data = array(
			'openid_op_endpoint'    => 'http://localhost',
			'openid_ns_ax'          => 'http://openid.net/srv/ax/1.0',
			'openid_ax_mode'        => 'fetch_response',
			'openid_ax_type_fname'  => 'http://example.com/schema/fullname',
			'openid_ax_value_fname' => 'John Smith',
			'openid_ns_ay'          => 'http://openid.net/srv/ax/1.1',
			'openid_ay_mode'        => 'fetch_response',
			'openid_ay_type_fname'  => 'http://example.com/schema/fullname',
			'openid_ay_value_fname' => 'Foo Bar',
			'foo'                   => 'bar',
			'test'                  => 'test',
		);

		// ns http://openid.net/srv/ax/1.0
		$this->assertEquals(array(
			'mode'        => 'fetch_response',
			'type_fname'  => 'http://example.com/schema/fullname',
			'value_fname' => 'John Smith',
		), ProviderAbstract::getExtension($data, 'http://openid.net/srv/ax/1.0'));

		// ns http://openid.net/srv/ax/1.1
		$this->assertEquals(array(
			'mode'        => 'fetch_response',
			'type_fname'  => 'http://example.com/schema/fullname',
			'value_fname' => 'Foo Bar',
		), ProviderAbstract::getExtension($data, 'http://openid.net/srv/ax/1.1'));
	}

	protected function getPaths()
	{
		return array(
			'/openid' => 'PSX\OpenId\TestProviderAbstract',
		);
	}

	protected function doAssociation($modulus, $gen, $consumerPublic)
	{
		$data     = http_build_query(array(
			'openid.ns'                 => 'http://specs.openid.net/auth/2.0', 
			'openid.mode'               => 'associate', 
			'openid.assoc_type'         => 'HMAC-SHA1', 
			'openid.session_type'       => 'DH-SHA1',
			'openid.dh_modulus'         => $modulus,
			'openid.dh_gen'             => $gen,
			'openid.dh_consumer_public' => $consumerPublic,
		), '', '&');
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/openid'), 'POST', array('Content-Type' => 'application/x-www-urlencoded'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$data = OpenId::keyValueDecode($body);

		$this->assertEquals('http://specs.openid.net/auth/2.0', $data['ns']);
		$this->assertEquals('46800', $data['expires_in']);
		$this->assertEquals('DH-SHA1', $data['session_type']);
		$this->assertEquals('HMAC-SHA1', $data['assoc_type']);

		return $data;
	}

	protected function doCheckidSetupRequest($assocHandle)
	{
		$url = new Url('http://127.0.0.1/openid');
		$url->addParam('openid_ns', 'http://specs.openid.net/auth/2.0');
		$url->addParam('openid_mode', 'checkid_setup');
		$url->addParam('openid_claimed_id', 'http://k42b3.com');
		$url->addParam('openid_identity', 'http://k42b3.com');
		$url->addParam('openid_assoc_handle', $assocHandle);
		$url->addParam('openid_return_to', 'http://127.0.0.1/callback');

		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request($url, 'GET');
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);

		return new Url((string) $response->getHeader('Location'));
	}

	protected function doCheckAuthentication(Url $url)
	{
		$params = $url->getParams();
		$params['openid_mode'] = 'check_authentication';

		$data     = http_build_query($params, '', '&');
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/openid'), 'POST', array('Content-Type' => 'application/x-www-urlencoded'), $data);
		$response = new Response();
		$response->setBody($body);

		$controller = $this->loadController($request, $response);
		$body       = (string) $response->getBody();

		$data = OpenId::keyValueDecode($body);

		$this->assertEquals('http://specs.openid.net/auth/2.0', $data['ns']);
		$this->assertEquals('true', $data['is_valid']);
	}

	protected function generateConsumerRequest()
	{
		$g = pack('H*', ProviderAbstract::DH_G);
		$p = pack('H*', ProviderAbstract::DH_P);

		$pkey    = new PKey(array('dh' => array('p' => $p, 'g' => $g)));
		$details = $pkey->getDetails();

		return array(
			'modulus'         => base64_encode(ProviderAbstract::btwoc($details['dh']['p'])),
			'gen'             => base64_encode(ProviderAbstract::btwoc($details['dh']['g'])),
			'consumer_public' => base64_encode(ProviderAbstract::btwoc($details['dh']['pub_key'])),
			'pkey'            => $pkey,
		);
	}
}


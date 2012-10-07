<?php
/*
 *  $Id: ProviderAbstractTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_OpenId_ProviderAbstractTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_OpenId_ProviderAbstractTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
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

		), PSX_OpenId_ProviderAbstract::getExtension($data, 'http://openid.net/srv/ax/1.0'));

		// ns http://openid.net/srv/ax/1.1
		$this->assertEquals(array(

			'mode'        => 'fetch_response',
			'type_fname'  => 'http://example.com/schema/fullname',
			'value_fname' => 'Foo Bar',

		), PSX_OpenId_ProviderAbstract::getExtension($data, 'http://openid.net/srv/ax/1.1'));
	}

	public function testDhSha1Round1()
	{
		$this->testDhSha1();
	}

	public function testDhSha1Round2()
	{
		$this->testDhSha1();
	}

	public function testDhSha1Round3()
	{
		$this->testDhSha1();
	}

	public function testDhSha1Round4()
	{
		$this->testDhSha1();
	}

	public function testDhSha1Round5()
	{
		$this->testDhSha1();
	}

	public function testDhSha1Round6()
	{
		$this->testDhSha1();
	}

	public function testDhSha1()
	{
		// generate consumer
		$request = $this->generateConsumerRequest();

		// generate server
		$dhGen         = $request['gen'];
		$dhModulus     = $request['modulus'];
		$dhConsumerPub = $request['consumer_public'];
		$dhFunc        = 'SHA1';
		$secret        = PSX_OpenId_ProviderAbstract::randomBytes(20);

		$res = PSX_OpenId_ProviderAbstract::generateDh($dhGen, $dhModulus, $dhConsumerPub, $dhFunc, $secret);

		$this->assertEquals(true, isset($res['pubKey']));
		$this->assertEquals(true, isset($res['macKey']));

		// calculate condumer
		$serverPub    = base64_decode($res['pubKey']);
		$dhSec        = PSX_OpenSsl::dhComputeKey($serverPub, $request['pkey']);
		$sec          = PSX_OpenSsl::digest(PSX_OpenId_ProviderAbstract::btwoc($dhSec), $dhFunc, true);
		$serverSecret = $sec ^ base64_decode($res['macKey']);

		// compare with server
		$this->assertEquals(true, $secret === $serverSecret);
	}

	private function generateConsumerRequest()
	{
		$g = pack('H*', PSX_OpenId_ProviderAbstract::DH_G);
		$p = pack('H*', PSX_OpenId_ProviderAbstract::DH_P);

		$pkey    = new PSX_OpenSsl_PKey(array('dh' => array('p' => $p, 'g' => $g)));
		$details = $pkey->getDetails();

		return array(
			'modulus'         => base64_encode(PSX_OpenId_ProviderAbstract::btwoc($details['dh']['p'])),
			'gen'             => base64_encode(PSX_OpenId_ProviderAbstract::btwoc($details['dh']['g'])),
			'consumer_public' => base64_encode(PSX_OpenId_ProviderAbstract::btwoc($details['dh']['pub_key'])),
			'pkey'            => $pkey,
		);
	}
}


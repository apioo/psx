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

namespace PSX\OpenId;

use PSX\OpenId;
use PSX\OpenSsl;
use PSX\OpenSsl\PKey;

/**
 * GenerateDhTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GenerateDhTest extends \PHPUnit_Framework_TestCase
{
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
		$secret        = ProviderAbstract::randomBytes(20);

		$res = ProviderAbstract::generateDh($dhGen, $dhModulus, $dhConsumerPub, $dhFunc, $secret);

		$this->assertEquals(true, isset($res['pubKey']));
		$this->assertEquals(true, isset($res['macKey']));

		// calculate consumer
		$serverPub    = base64_decode($res['pubKey']);
		$dhSec        = OpenSsl::dhComputeKey($serverPub, $request['pkey']);
		$sec          = OpenSsl::digest(ProviderAbstract::btwoc($dhSec), $dhFunc, true);
		$serverSecret = $sec ^ base64_decode($res['macKey']);

		// compare with server
		$this->assertEquals(true, $secret === $serverSecret);
	}

	private function generateConsumerRequest()
	{
		$g = pack('H*', ProviderAbstract::DH_G);
		$p = pack('H*', ProviderAbstract::DH_P);

		$pkey = new PKey(array(
			'private_key_type' => OPENSSL_KEYTYPE_DH,
			'dh' => array('p' => $p, 'g' => $g
		)));

		$details = $pkey->getDetails();

		return array(
			'modulus'         => base64_encode(ProviderAbstract::btwoc($details['dh']['p'])),
			'gen'             => base64_encode(ProviderAbstract::btwoc($details['dh']['g'])),
			'consumer_public' => base64_encode(ProviderAbstract::btwoc($details['dh']['pub_key'])),
			'pkey'            => $pkey,
		);
	}
}

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

use PSX\OpenSsl\Pkey;

/**
 * OpenSslTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OpenSslTest extends \PHPUnit_Framework_TestCase
{
	public function testEncryptDecrypt()
	{
		$data   = 'Secret text';
		$key    = 'foobar';
		$method = 'aes-128-cbc';
		$iv     = substr(md5('foo'), 4, 16);

		$encrypt = OpenSsl::encrypt($data, $method, $key, 0, $iv);

		$this->assertEquals('U1dIdXBaY25uOTRaZ3dhZ1l6QzQwZz09', base64_encode($encrypt));

		$decrypt = OpenSsl::decrypt($encrypt, $method, $key, 0, $iv);

		$this->assertEquals($data, $decrypt);
	}

	public function testDigest()
	{
		$methods = OpenSsl::getMdMethods();

		$this->assertTrue(is_array($methods));
		$this->assertTrue(count($methods) > 0);

		$data = OpenSsl::digest('foobar', 'SHA256');

		$this->assertEquals('c3ab8ff13720e8ad9047dd39466b3c8974e592c2fa383d4a3960714caef0c4f2', $data);
	}

	public function testgetCipherMethods()
	{
		$methods = OpenSsl::getCipherMethods();

		$this->assertTrue(is_array($methods));
		$this->assertTrue(count($methods) > 0);
	}

	public function testOpenSeal()
	{
		$data = 'Some content';

		$key = $this->getKey();
		$key->export($privateKey, 'foobar');

		OpenSsl::seal($data, $sealed, $ekeys, array($key));

		$sealed = base64_encode($sealed);
		$envKey = base64_encode($ekeys[0]);

		OpenSsl::open(base64_decode($sealed), $opened, base64_decode($envKey), $key);

		$key->free();

		$this->assertEquals($data, $opened);
	}

	public function testSignVerify()
	{
		$pkey = $this->getKey();

		$data = 'Some content';

		OpenSsl::sign($data, $signature, $pkey);

		$result = OpenSsl::verify($data, $signature, $pkey);

		$this->assertEquals('ldkl10vQQX+CMcfcu2qv8GaTDL58DBWqu13Snk5N5caG02KcoHDkfjyDeRM75GMmvjpxYEtf23R/wmYCeljdyOJPPolPdyAFqatkrMqHOd3VPFcLZpRMzb6bZAY4q+aUejxMRIqXFdc3TN6msb/PYrk3pJg0W9Svi9In8Hvil9U=', base64_encode($signature));
		$this->assertEquals(1, $result);

		$data = 'Some content corrupted';

		$result = OpenSsl::verify($data, $signature, $pkey);

		$this->assertEquals(0, $result);

		$pkey->free();
	}

	public function testPublicEncryptPrivateDecrypt()
	{
		$pkey = $this->getKey();
		$pkey->export($privateKey, 'foobar');

		$data = 'Secret content';

		OpenSsl::publicEncrypt($data, $crypted, $pkey);

		$this->assertNotEmpty($crypted);

		OpenSsl::privateDecrypt($crypted, $decrypted, $pkey);

		$this->assertEquals($data, $decrypted);
	}

	public function testPrivateEncryptPublicDecrypt()
	{
		$pkey = $this->getKey();
		$pkey->export($privateKey, 'foobar');

		$data = 'Secret content';

		OpenSsl::privateEncrypt($data, $crypted, $pkey);

		$this->assertNotEmpty($crypted);

		OpenSsl::publicDecrypt($crypted, $decrypted, $pkey);

		$this->assertEquals($data, $decrypted);
	}

	public function testRandomPseudoBytes()
	{
		$data = OpenSsl::randomPseudoBytes(8);

		$this->assertEquals(8, strlen($data));
	}

	public function testErrorString()
	{
		$message = OpenSsl::errorString();

		$this->assertEquals('', $message);
	}

	protected function getKey()
	{
		$privateKey = <<<TEXT
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,F653AE67D69C3B31

tlStQDd5F+sCevx+gEf+pZpIL3Fh0rvHfjJjygPiDERuzZ4qTVAuSHshad3nmOfM
tRJEeBTVGekJqd4ztxXgKl3yhXLmdDZQ9X0PtPvasQ8eGsTkuxHSmdeGwUEDvScu
0eKOSj6P9Q3kYg6RyA0G7tsNETnxCj3G4S1y8ErO/Y/zZRytj26RgBbRACHfb/3I
/9EEVc1y3EjTxTZiRtSyKQeEVAkx2wcCVQCIEnozP+4vG1TiDLJyk0810iqkT3II
Hhssl4YrQ4JtVtgj9m4lmHAbCL/QkIyheFWWilYYhVQm06Wmt65Evj/Ahd/mCpBb
KPUoZgNWvVUbG8KuwoV5uan/8lyTxBo3eZOeHuA1fD5fmuEp8s0FVUlZNwylAmU9
X0H2Snv7YtFjQIW1CLFC/xl0oKc/m+i1t1GIekmOWjiIdt2auD5RhDyGAnJmrVf4
yIcCq8tA+X0+KQHrDkhuAt/cwpChtPIMglXibn4J7dZTtqrhrIpX/7kyW5rk5U/g
Gzl/VVjyU/Ek//bwcs3n6ZtIayAYEa6InCBiisGGJfIdVtSW0MkN9+LQHo0xPlqr
bDe6fhtlF5gczEIj3R9zdZg0fZwTijFhNi+AVsfHHVGXR/StRLGyUAsuHgzEVb57
rx/FITQNinI+hukPnJK9XSYfsEp6m0yYsxKQl9LpE370kCB6W5M0etfpAIHLCOsf
NLdCEZh1YcQ9pIu2wHisIe8QgRmdMtR0LyenlwrgOK1cHh5Xhye9oGRb0vYOb3vb
5m9+zCBc8/5Ud68+H8aT+jiEHYQ9i22GmqaKDL72C2scuUJllJ0zrA==
-----END RSA PRIVATE KEY-----
TEXT;

		return PKey::getPrivate($privateKey, 'foobar');
	}
}

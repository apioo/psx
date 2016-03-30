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

namespace PSX\OpenSsl\Tests;

use PSX\OpenSsl\OpenSsl;
use PSX\OpenSsl\PKey;

/**
 * OpenSslTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenSslTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!function_exists('openssl_pkey_new')) {
            $this->markTestSkipped('Openssl extension not installed');
        }
    }

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

    /**
     * This is essentially the openid association flow where two parties
     * establish a shared secret. Only the server/client public key and mac key
     * are transfered over the wire. The shared secret can then be used to
     * encrypt or sign data
     */
    public function testDhComputeKey()
    {
        if (getenv('TRAVIS_PHP_VERSION') == 'hhvm') {
            $this->markTestSkipped('Key type DH is not supported');
        }

        // both parties must know these parameters
        $p      = pack('H*', 'dcf93a0b883972ec0e19989ac5a2ce310e1d37717e8d9571bb7623731866e61ef75a2e27898b057f9891c2e27a639c3f29b60814581cd3b2ca3986d2683705577d45c2e7e52dc81c7a171876e5cea74b1448bfdfaf18828efd2519f14e45e3826634af1949e5b535cc829a483b8a76223e5d490a257f05bdff16f2fb22c583ab');
        $g      = pack('H*', '02');
        $dhFunc = 'SHA256';

        // the client generates a new key
        $clientKey = new PKey(array(
            'private_key_type' => OPENSSL_KEYTYPE_DH,
            'dh' => array(
                'p' => $p,
                'g' => $g,
            )
        ));

        $details         = $clientKey->getDetails();
        $clientPublicKey = $details['dh']['pub_key'];

        // the server receives the public key of the client

        // the server generates a random secret
        $secret = OpenSsl::randomPseudoBytes(32);

        // the server creates a new key
        $serverKey = new PKey(array(
            'private_key_type' => OPENSSL_KEYTYPE_DH,
            'dh' => array(
                'p' => $p,
                'g' => $g,
            )
        ));

        $details         = $serverKey->getDetails();
        $serverPublicKey = $details['dh']['pub_key'];

        // the server generates the dh key
        $dhKey  = OpenSsl::dhComputeKey($clientPublicKey, $serverKey);
        $digest = OpenSsl::digest($dhKey, $dhFunc, true);
        $macKey = $digest ^ $secret;

        // the client receives the public key and mac key of the server
        $dhKey  = OpenSsl::dhComputeKey($serverPublicKey, $clientKey);
        $digest = OpenSsl::digest($dhKey, $dhFunc, true);
        $result = $digest ^ $macKey;

        // we have established a shared secret

        $this->assertEquals($secret, $result);
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

    /**
     * @expectedException \PSX\OpenSsl\Exception
     */
    public function testSealInvalidPubKeyType()
    {
        $data = 'Some content';

        OpenSsl::seal($data, $sealed, $ekeys, array('foo'));
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

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

use PSX\OpenSsl\PKey;

/**
 * PKeyTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PKeyTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!function_exists('openssl_pkey_new')) {
            $this->markTestSkipped('Openssl extension not installed');
        }
    }

    public function testExport()
    {
        $pkey = new PKey();
        $pkey->export($privateKey, 'foobar');

        $publicKey = $pkey->getPublicKey();

        $pkey->free();

        $this->assertEquals('-----BEGIN PUBLIC KEY-----', substr($publicKey, 0, 26));
    }

    public function testGetPrivate()
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

        $pkey = PKey::getPrivate($privateKey, 'foobar');

        $this->assertInstanceOf('PSX\OpenSsl\PKey', $pkey);

        $publicKey = $pkey->getPublicKey();

        $pkey->free();

        $this->assertEquals('-----BEGIN PUBLIC KEY-----', substr($publicKey, 0, 26));
    }

    /**
     * @expectedException \PSX\OpenSsl\Exception
     */
    public function testGetPrivateInvalidPassword()
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

        $pkey = PKey::getPrivate($privateKey, 'foo');
    }

    /**
     * @expectedException \PSX\OpenSsl\Exception
     */
    public function testGetPrivateInvalidFormat()
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
X0H2Snv7YtFjQIW1CLFC/xl0oKc/m+i1t1GIekmOWjiIdt2auD5RhDyGAnJmrVf4
yIcCq8tA+X0+KQHrDkhuAt/cwpChtPIMglXibn4J7dZTtqrhrIpX/7kyW5rk5U/g
Gzl/VVjyU/Ek//bwcs3n6ZtIayAYEa6InCBiisGGJfIdVtSW0MkN9+LQHo0xPlqr
bDe6fhtlF5gczEIj3R9zdZg0fZwTijFhNi+AVsfHHVGXR/StRLGyUAsuHgzEVb57
rx/FITQNinI+hukPnJK9XSYfsEp6m0yYsxKQl9LpE370kCB6W5M0etfpAIHLCOsf
NLdCEZh1YcQ9pIu2wHisIe8QgRmdMtR0LyenlwrgOK1cHh5Xhye9oGRb0vYOb3vb
5m9+zCBc8/5Ud68+H8aT+jiEHYQ9i22GmqaKDL72C2scuUJllJ0zrA==
-----END RSA PRIVATE KEY-----
TEXT;

        $pkey = PKey::getPrivate($privateKey, 'foobar');
    }

    public function testGetPublic()
    {
        $publicKey = <<<TEXT
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDK5CRsyemwJ0Pf09ww0UYaeqUr
YDkaD+6KaPGMNhkvKyWvLWn0T3cJ9m2oG9/KB8x3Nx/U1Dqc+pPAUIuJS/kA9sVn
07BCbq+O4+xZpwOR7eDAGm1z5Q7hsTG0gW2k5mEDgdxmapKV8IOvXj5FkobYVw8W
ZOqvOq7faORkrJPT1QIDAQAB
-----END PUBLIC KEY-----
TEXT;

        $pkey = PKey::getPublic($publicKey);

        $this->assertInstanceOf('PSX\OpenSsl\PKey', $pkey);

        $publicKey = $pkey->getPublicKey();

        $pkey->free();

        $this->assertEquals('-----BEGIN PUBLIC KEY-----', substr($publicKey, 0, 26));
    }

    /**
     * @expectedException \PSX\OpenSsl\Exception
     */
    public function testGetPublicInvalidFormat()
    {
        $publicKey = <<<TEXT
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDK5CRsyemwJ0Pf09ww0UYaeqUr
07BCbq+O4+xZpwOR7eDAGm1z5Q7hsTG0gW2k5mEDgdxmapKV8IOvXj5FkobYVw8W
ZOqvOq7faORkrJPT1QIDAQAB
-----END PUBLIC KEY-----
TEXT;

        $pkey = PKey::getPublic($publicKey);
    }
}

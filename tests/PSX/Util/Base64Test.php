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

namespace PSX\Util;

/**
 * Base64Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Base64Test extends \PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $this->assertEquals('', Base64::encode(''));
        $this->assertEquals('Zg==', Base64::encode('f'));
        $this->assertEquals('Zm8=', Base64::encode('fo'));
        $this->assertEquals('Zm9v', Base64::encode('foo'));
        $this->assertEquals('Zm9vYg==', Base64::encode('foob'));
        $this->assertEquals('Zm9vYmE=', Base64::encode('fooba'));
        $this->assertEquals('Zm9vYmFy', Base64::encode('foobar'));

        for ($i = 0; $i < 16; $i++) {
            $data = hash('md5', $i, true);

            $this->assertEquals(base64_encode($data), Base64::encode($data));
        }
    }

    public function testDecode()
    {
        $this->assertEquals('f', Base64::decode('Zg=='));
        $this->assertEquals('fo', Base64::decode('Zm8='));
        $this->assertEquals('foo', Base64::decode('Zm9v'));
        $this->assertEquals('foob', Base64::decode('Zm9vYg=='));
        $this->assertEquals('fooba', Base64::decode('Zm9vYmE='));
        $this->assertEquals('foobar', Base64::decode('Zm9vYmFy'));
    }
}

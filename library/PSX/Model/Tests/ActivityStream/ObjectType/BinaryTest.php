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

namespace PSX\Model\Tests\ActivityStream\ObjectType;

use PSX\Data\Tests\SerializeTestAbstract;
use PSX\Model\ActivityStream\ObjectType;
use PSX\Model\ActivityStream\ObjectType\Binary;

/**
 * BinaryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BinaryTest extends SerializeTestAbstract
{
    public function testBinary()
    {
        $binary = new Binary();
        $binary->setCompression('deflate');
        $binary->setData('dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=');
        $binary->setFileUrl('foo.txt');
        $binary->setLength(25);
        $binary->setMd5('827ae7e1ab45e4dd591d087c741e5770');
        $binary->setMimeType('text/plain');

        $content = <<<JSON
{
  "objectType": "binary",
  "compression": "deflate",
  "data": "dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=",
  "fileUrl": "foo.txt",
  "length": 25,
  "md5": "827ae7e1ab45e4dd591d087c741e5770",
  "mimeType": "text/plain"
}
JSON;

        $this->assertRecordEqualsContent($binary, $content);

        $this->assertEquals('deflate', $binary->getCompression());
        $this->assertEquals('dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=', $binary->getData());
        $this->assertEquals('foo.txt', $binary->getFileUrl());
        $this->assertEquals(25, $binary->getLength());
        $this->assertEquals('827ae7e1ab45e4dd591d087c741e5770', $binary->getMd5());
        $this->assertEquals('text/plain', $binary->getMimeType());
    }
}

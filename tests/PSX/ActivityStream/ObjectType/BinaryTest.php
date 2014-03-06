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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\LinkObject;
use PSX\ActivityStream\Object;
use PSX\DateTime;
use PSX\Data\SerializeTestAbstract;

/**
 * BinaryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BinaryTest extends SerializeTestAbstract
{
	public function testBinary()
	{
		$stream = new LinkObject();
		$stream->setUrl('http://example.org/my_binary.mp3');

		$binary = new Binary();
		$binary->setCompression('deflate');
		$binary->setData('dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=');
		$binary->setFileUrl('foo.txt');
		$binary->setLength(25);
		$binary->setMd5('827ae7e1ab45e4dd591d087c741e5770');
		$binary->setMimeType('text/plain');

		$object = new Object();
		$object->setObjectType('note');
		$object->setDisplayName('A note with a binary attachment');
		$object->setAttachments(array($binary));

		$content = <<<JSON
  {
    "objectType": "note",
    "displayName": "A note with a binary attachment",
    "attachments": [
	  {
	    "objectType": "binary",
	    "compression": "deflate",
	    "data": "dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=",
	    "fileUrl": "foo.txt",
	    "length": 25,
	    "md5": "827ae7e1ab45e4dd591d087c741e5770",
	    "mimeType": "text/plain"
	  }
    ]
  }
JSON;

		$this->assertRecordEqualsContent($object, $content);

		$this->assertEquals('deflate', $binary->getCompression());
		$this->assertEquals('dGhpcyBpcyB1bmNvbXByZXNzZWQgZGF0YQo=', $binary->getData());
		$this->assertEquals('foo.txt', $binary->getFileUrl());
		$this->assertEquals(25, $binary->getLength());
		$this->assertEquals('827ae7e1ab45e4dd591d087c741e5770', $binary->getMd5());
		$this->assertEquals('text/plain', $binary->getMimeType());
	}
}

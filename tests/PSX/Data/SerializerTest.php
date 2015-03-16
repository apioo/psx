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

namespace PSX\Data;

use Doctrine\Common\Annotations\AnnotationRegistry;
use PSX\Api\Version;

/**
 * SerializerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
	public function testSerialize()
	{
		$author = new Serializer\TestAuthor();
		$author->setName('bar');

		$object = new Serializer\TestObject();
		$object->setTitle('foo');
		$object->setAuthor($author);

		$return = getContainer()->get('serializer')->serialize($object);

		$expect = new \stdClass();
		$expect->title = 'foo';
		$expect->author = new \stdClass();
		$expect->author->name = 'bar';

		$this->assertEquals($expect, $return);
	}

	public function testSerializeVersioned()
	{
		$author = new Serializer\TestAuthor();
		$author->setName('bar');

		$object = new Serializer\TestObjectVersioned();
		$object->setTitle('foo');
		$object->setAuthor($author);

		$return = getContainer()->get('serializer')->serialize($object, new Version(1));

		$expect = new \stdClass();
		$expect->title = 'foo';

		$this->assertEquals($expect, $return);
	}

	public function testSerializeVersionedGreater()
	{
		$author = new Serializer\TestAuthor();
		$author->setName('bar');

		$object = new Serializer\TestObjectVersioned();
		$object->setTitle('foo');
		$object->setAuthor($author);

		$return = getContainer()->get('serializer')->serialize($object, new Version(2));

		$expect = new \stdClass();
		$expect->title = 'foo';
		$expect->author = new \stdClass();
		$expect->author->name = 'bar';

		$this->assertEquals($expect, $return);
	}
}

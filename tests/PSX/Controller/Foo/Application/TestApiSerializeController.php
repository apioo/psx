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

namespace PSX\Controller\Foo\Application;

use PSX\Controller\ApiAbstract;
use PSX\Data\Serializer;

/**
 * TestApiSerializeController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestApiSerializeController extends ApiAbstract
{
	/**
	 * @Inject
	 * @var PHPUnit_Framework_TestCase
	 */
	protected $testCase;

	/**
	 * @Inject
	 * @var PSX\Data\SerializerInterface
	 */
	protected $serializer;

	public function doAll()
	{
		$this->setBody(array(
			'entry' => $this->serializer->serialize($this->getEntry())
		));
	}

	protected function getEntry()
	{
		$entries = array();

		$author = new Serializer\TestAuthor();
		$author->setName('bar');

		$object = new Serializer\TestObject();
		$object->setTitle('foo');
		$object->setAuthor($author);
		$object->setContributors([$author, $author]);
		$object->setTags(['foo', 'bar']);

		$entries[] = $object;

		$author = new Serializer\TestAuthor();
		$author->setName('bar');

		$object = new Serializer\TestObject();
		$object->setTitle('bar');
		$object->setAuthor($author);
		$object->setContributors([$author, $author]);
		$object->setTags(['foo', 'bar']);

		$entries[] = $object;

		return $entries;
	}
}

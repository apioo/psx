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

namespace PSX\Controller\Foo\Application\SchemaApi;

use PSX\Api\Documentation;
use PSX\Api\Documentation\Parser;
use PSX\Api\Version;
use PSX\Api\Resource;
use PSX\Controller\SchemaApiAbstract;
use PSX\Data\RecordInterface;
use PSX\Loader\Context;
use PSX\Controller\SchemaApi\PropertyDocumentationTest;

/**
 * PropertyRamlController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyRamlController extends SchemaApiAbstract
{
	/**
	 * @Inject
	 * @var PHPUnit_Framework_TestCase
	 */
	protected $testCase;

	public function getDocumentation()
	{
		return Parser\Raml::fromFile(__DIR__ . '/../../Resource/property.raml', $this->context->get(Context::KEY_PATH));
	}

	protected function doGet(Version $version)
	{
		return PropertyDocumentationTest::getDataByType($this->queryParameters->getProperty('type'));
	}

	protected function doCreate(RecordInterface $record, Version $version)
	{
		$this->testCase->assertEquals(['bar'], $record->getArray());
		$this->testCase->assertEquals(true, $record->getBoolean());
		$this->testCase->assertEquals(['foo' => 'bar'], $record->getComplex()->getRecordInfo()->getFields());
		$this->testCase->assertEquals('2015-05-01', $record->getDate()->format('Y-m-d'));
		$this->testCase->assertEquals('2015-05-01T13:37:14Z', $record->getDateTime()->format('Y-m-d\TH:i:s\Z'));
		$this->testCase->assertEquals('000100000000', $record->getDuration()->format('%Y%M%D%H%I%S'));
		$this->testCase->assertEquals(13.37, $record->getFloat());
		$this->testCase->assertEquals(7, $record->getInteger());
		$this->testCase->assertEquals('bar', $record->getString());
		$this->testCase->assertEquals('13:37:14', $record->getTime()->format('H:i:s'));

		return $record;
	}

	protected function doUpdate(RecordInterface $record, Version $version)
	{
	}

	protected function doDelete(RecordInterface $record, Version $version)
	{
	}
}

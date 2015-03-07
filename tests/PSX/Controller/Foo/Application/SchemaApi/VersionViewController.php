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
use PSX\Api\Version;
use PSX\Api\View;
use PSX\Data\RecordInterface;
use PSX\Controller\SchemaApiAbstract;
use PSX\Loader\Context;

/**
 * VersionViewController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VersionViewController extends SchemaApiAbstract
{
	/**
	 * @Inject
	 * @var PSX\Data\Schema\SchemaManager
	 */
	protected $schemaManager;

	/**
	 * @Inject
	 * @var PHPUnit_Framework_TestCase
	 */
	protected $testCase;

	public function getDocumentation()
	{
		$responseSchema = $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage');

		$path    = $this->context->get(Context::KEY_PATH);
		$version = new Documentation\Version();

		$builder = new View\Builder(View::STATUS_CLOSED, $path);
		$builder->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
		$builder->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
		$builder->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
		$builder->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

		$version->addView(1, $builder->getView());

		$builder = new View\Builder(View::STATUS_DEPRECATED, $path);
		$builder->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
		$builder->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
		$builder->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
		$builder->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

		$version->addView(2, $builder->getView());

		$builder = new View\Builder(View::STATUS_ACTIVE, $path);
		$builder->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
		$builder->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
		$builder->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
		$builder->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

		$version->addView(3, $builder->getView());

		return $version;
	}

	protected function doGet(Version $version)
	{
		return array(
			'entry' => getContainer()->get('table_manager')->getTable('PSX\Sql\TestTable')->getAll()
		);
	}

	protected function doCreate(RecordInterface $record, Version $version)
	{
		$this->testCase->assertEquals(3, $record->getUserId());
		$this->testCase->assertEquals('test', $record->getTitle());
		$this->testCase->assertInstanceOf('DateTime', $record->getDate());

		return array(
			'success' => true,
			'message' => 'You have successful create a record'
		);
	}

	protected function doUpdate(RecordInterface $record, Version $version)
	{
		$this->testCase->assertEquals(1, $record->getId());
		$this->testCase->assertEquals(3, $record->getUserId());
		$this->testCase->assertEquals('foobar', $record->getTitle());

		return array(
			'success' => true,
			'message' => 'You have successful update a record'
		);
	}

	protected function doDelete(RecordInterface $record, Version $version)
	{
		$this->testCase->assertEquals(1, $record->getId());

		return array(
			'success' => true,
			'message' => 'You have successful delete a record'
		);
	}
}

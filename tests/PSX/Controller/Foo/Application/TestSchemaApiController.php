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

namespace PSX\Controller\Foo\Application;

use PSX\Data\RecordInterface;
use PSX\Data\Schema\ApiDocumentation;
use PSX\Controller\SchemaApiAbstract;

/**
 * TestSchemaApiController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestSchemaApiController extends SchemaApiAbstract
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

	protected function doGet()
	{
		return array(
			'entry' => getContainer()->get('table_manager')->getTable('PSX\Handler\Table\TestTable')->getAll()
		);
	}

	protected function doCreate(RecordInterface $record)
	{
		$this->testCase->assertEquals(3, $record->getUserId());
		$this->testCase->assertEquals('test', $record->getTitle());
		$this->testCase->assertInstanceOf('DateTime', $record->getDate());

		return array(
			'success' => true,
			'message' => 'You have successful create a record'
		);
	}

	protected function doUpdate(RecordInterface $record)
	{
		$this->testCase->assertEquals(1, $record->getId());
		$this->testCase->assertEquals(3, $record->getUserId());
		$this->testCase->assertEquals('foobar', $record->getTitle());

		return array(
			'success' => true,
			'message' => 'You have successful update a record'
		);
	}

	protected function doDelete(RecordInterface $record)
	{
		$this->testCase->assertEquals(1, $record->getId());

		return array(
			'success' => true,
			'message' => 'You have successful delete a record'
		);
	}

	public function getSchemaDocumentation()
	{
		$responseSchema = $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage');

		$doc = new ApiDocumentation();
		$doc->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
		$doc->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
		$doc->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
		$doc->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

		return $doc;
	}
}

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

namespace PSX\Data\Record;

/**
 * ImportManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ImportManagerTest extends \PHPUnit_Framework_TestCase
{
	public function testGetImporterBySource()
	{
		$manager = new ImporterManager();
		$source = new \stdClass();

		$acceptImporter = $this->getMockBuilder('PSX\Data\Record\ImporterInterface')
			->getMock();

		$acceptImporter->expects($this->once())
			->method('accept')
			->with($source)
			->will($this->returnValue(true));

		$notAcceptImporter = $this->getMockBuilder('PSX\Data\Record\ImporterInterface')
			->getMock();

		$notAcceptImporter->expects($this->once())
			->method('accept')
			->with($source)
			->will($this->returnValue(false));

		$manager->addImporter($notAcceptImporter);
		$manager->addImporter($acceptImporter);

		$importer = $manager->getImporterBySource($source);

		$this->assertTrue($acceptImporter === $importer);
	}

	public function testGetImporterBySourceNotAvailable()
	{
		$manager = new ImporterManager();
		$source = new \stdClass();

		$notAcceptImporter = $this->getMockBuilder('PSX\Data\Record\ImporterInterface')
			->getMock();

		$notAcceptImporter->expects($this->once())
			->method('accept')
			->with($source)
			->will($this->returnValue(false));

		$manager->addImporter($notAcceptImporter);

		$importer = $manager->getImporterBySource($source);

		$this->assertEquals(null, $importer);
	}

	public function testGetImporterByInstance()
	{
		$manager = new ImporterManager();

		$acceptImporter = $this->getMockBuilder('PSX\Data\Record\ImporterInterface')
			->getMock();

		$manager->addImporter(new Importer\Table());
		$manager->addImporter($acceptImporter);

		$importer = $manager->getImporterByInstance(get_class($acceptImporter));

		$this->assertTrue($acceptImporter === $importer);

		$importer = $manager->getImporterByInstance('stdClass');

		$this->assertEquals(null, $importer);
	}
}

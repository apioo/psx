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

namespace PSX\Data\Record;

/**
 * ImportManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

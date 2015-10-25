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

use PSX\Data\Record;
use PSX\Data\Schema;
use PSX\Data\Schema\Property;
use PSX\Test\Environment;

/**
 * ImportManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ImportManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetImporterBySourceRecord()
    {
        $source = $this->getMockBuilder('PSX\Data\Record')
            ->disableOriginalConstructor()
            ->getMock();

        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterBySource($source);

        $this->assertInstanceOf('PSX\Data\Record\Importer\Record', $importer);
    }

    public function testGetImporterBySourceSchema()
    {
        $source = $this->getMockBuilder('PSX\Data\Schema')
            ->disableOriginalConstructor()
            ->getMock();

        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterBySource($source);

        $this->assertInstanceOf('PSX\Data\Record\Importer\Schema', $importer);
    }

    public function testGetImporterBySourceTable()
    {
        $source = $this->getMockBuilder('PSX\Sql\Table')
            ->disableOriginalConstructor()
            ->getMock();

        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterBySource($source);

        $this->assertInstanceOf('PSX\Data\Record\Importer\Table', $importer);
    }

    public function testGetImporterBySourceUnknown()
    {
        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterBySource(new \stdClass);

        $this->assertNull($importer);
    }

    public function testGetImporterByInstanceRecord()
    {
        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterByInstance('PSX\Data\Record\Importer\Record');

        $this->assertInstanceOf('PSX\Data\Record\Importer\Record', $importer);
    }

    public function testGetImporterByInstanceSchema()
    {
        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterByInstance('PSX\Data\Record\Importer\Schema');

        $this->assertInstanceOf('PSX\Data\Record\Importer\Schema', $importer);
    }

    public function testGetImporterByInstanceTable()
    {
        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterByInstance('PSX\Data\Record\Importer\Table');

        $this->assertInstanceOf('PSX\Data\Record\Importer\Table', $importer);
    }

    public function testGetImporterByInstanceUnknown()
    {
        $manager  = new ImporterManager(Environment::getService('record_factory_factory'));
        $importer = $manager->getImporterByInstance('PSX\Data\Record\Importer\Foo');

        $this->assertNull($importer);
    }
}

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

use PSX\Data\RecordInterface;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\Importer;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Assimilator;
use PSX\Sql\TableInterface;

/**
 * The importer manager returns the fitting importer for a source. The importer
 * manager returns always a new instance of the importer object
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ImporterManager
{
    /**
     * @var \PSX\Data\Record\FactoryFactory
     */
    protected $factoryFactory;

    public function __construct(FactoryFactory $factoryFactory)
    {
        $this->factoryFactory = $factoryFactory;
    }

    /**
     * Returns the fitting importer for the source
     *
     * @param mixed $source
     * @return \PSX\Data\Record\ImporterInterface
     */
    public function getImporterBySource($source)
    {
        return $this->createByClass($this->getClassForSource($source));
    }

    /**
     * Returns the importer which is an instance of the given class name
     *
     * @param string $className
     * @return \PSX\Data\Record\ImporterInterface
     */
    public function getImporterByInstance($className)
    {
        return $this->createByClass($className);
    }

    protected function getClassForSource($source)
    {
        if ($source instanceof RecordInterface) {
            return ImporterInterface::RECORD;
        } elseif ($source instanceof SchemaInterface) {
            return ImporterInterface::SCHEMA;
        } elseif ($source instanceof TableInterface) {
            return ImporterInterface::TABLE;
        }

        return null;
    }

    protected function createByClass($className)
    {
        switch ($className) {
            case ImporterInterface::RECORD:
                return new Importer\Record($this->factoryFactory);
                break;

            case ImporterInterface::SCHEMA:
                return new Importer\Schema(new Assimilator($this->factoryFactory));
                break;

            case ImporterInterface::TABLE:
                return new Importer\Table();
                break;
        }

        return null;
    }
}

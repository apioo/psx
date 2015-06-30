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

use PSX\Util\PriorityQueue;

/**
 * The importer manager returns the fitting importer for an source
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ImporterManager
{
    /**
     * @var \PSX\Data\Record\ImporterInterface[]
     */
	protected $importers;

	public function __construct()
	{
		$this->importers = new PriorityQueue();
	}

	public function addImporter(ImporterInterface $importer, $priority = 0)
	{
		$this->importers->insert($importer, $priority);
	}

	/**
	 * Returns the fitting importer for the source
	 *
     * @param mixed $source
	 * @return \PSX\Data\Record\ImporterInterface
	 */
	public function getImporterBySource($source)
	{
		foreach($this->importers as $importer)
		{
			if($importer->accept($source))
			{
				return $importer;
			}
		}

		return null;
	}

	/**
	 * Returns the importer which is an instanceof the given class name
	 *
     * @param string $className
	 * @return \PSX\Data\Record\ImporterInterface
	 */
	public function getImporterByInstance($className)
	{
		foreach($this->importers as $importer)
		{
			if($importer instanceof $className)
			{
				return $importer;
			}
		}

		return null;
	}
}

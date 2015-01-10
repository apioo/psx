<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Util\PriorityQueue;

/**
 * The importer manager returns the fitting importer for an source
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ImporterManager
{
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
	 * @return PSX\Data\Record\ImporterInterface
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
	 * @return PSX\Data\Record\ImporterInterface
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

<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data;

use PSX\Data\Record\ImporterInterface;
use PSX\Http\Message as HttpMessage;
use RuntimeException;

/**
 * ReaderAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ReaderAbstract implements ReaderInterface
{
	protected $importer;

	public function import(RecordInterface $record, HttpMessage $message)
	{
		$importer = $this->getDefaultImporter();

		if($importer instanceof Record\ImporterInterface)
		{
			$importer->import($record, $this->read($message));
		}
		else
		{
			throw new RuntimeException('Default importer not available');
		}
	}

	public function isContentTypeSupported($contentType)
	{
		return false;
	}

	public function getDefaultImporter()
	{
		return $this->importer;
	}

	public function setDefaultImporter(ImporterInterface $importer)
	{
		$this->importer = $importer;
	}
}

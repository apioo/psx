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

namespace PSX\Data;

use PSX\Data\Reader;
use PSX\Util\PriorityQueue;

/**
 * ReaderFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ReaderFactory
{
	protected $readers;

	public function __construct()
	{
		$this->readers = new PriorityQueue();
	}

	public function addReader(ReaderInterface $reader, $priority = 0)
	{
		$this->readers->insert($reader, $priority);
	}

	public function getDefaultReader()
	{
		return $this->readers->getIterator()->current();
	}

	public function getReaderByContentType($contentType, array $supportedReader = null)
	{
		$contentTypes = explode(',', $contentType);

		foreach($contentTypes as $contentType)
		{
			foreach($this->readers as $reader)
			{
				if($supportedReader !== null && !in_array(get_class($reader), $supportedReader))
				{
					continue;
				}

				if($reader->isContentTypeSupported($contentType))
				{
					return $reader;
				}
			}
		}

		return null;
	}

	public function getReaderByInstance($className)
	{
		foreach($this->readers as $reader)
		{
			if($reader instanceof $className)
			{
				return $reader;
			}
		}

		return null;
	}
}
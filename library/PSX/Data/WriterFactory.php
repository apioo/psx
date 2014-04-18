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

use PSX\Data\Writer;

/**
 * WriterFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterFactory
{
	protected $writers = array();

	public function addWriter(WriterInterface $writer, $priority = 0)
	{
		$this->writers[] = $writer;
	}

	public function getDefaultWriter()
	{
		return isset($this->writers[0]) ? $this->writers[0] : null;
	}

	public function getWriterByContentType($contentType, array $supportedWriter = null)
	{
		$contentTypes = explode(',', $contentType);

		foreach($contentTypes as $contentType)
		{
			foreach($this->writers as $writer)
			{
				if($supportedWriter !== null && !in_array(get_class($writer), $supportedWriter))
				{
					continue;
				}

				if($writer->isContentTypeSupported($contentType))
				{
					return $writer;
				}
			}
		}

		return null;
	}

	public function getWriterByInstance($className)
	{
		foreach($this->writers as $writer)
		{
			if($writer instanceof $className)
			{
				return $writer;
			}
		}

		return null;
	}

	public function getContentTypeByFormat($format)
	{
		foreach($this->writers as $writer)
		{
			$className = get_class($writer);
			$pos       = strrpos($className, '\\');
			$shortName = strtolower(substr($className, $pos === false ? 0 : $pos + 1));

			if($shortName == $format)
			{
				return $writer->getContentType();
			}
		}

		return null;
	}
}

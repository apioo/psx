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

namespace PSX\Data;

use PSX\Data\Writer;
use PSX\Http\MediaType;
use PSX\Http\Exception\NotAcceptableException;
use PSX\Util\PriorityQueue;

/**
 * WriterFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterFactory
{
	protected $writers;
	protected $contentNegotiation = array();

	public function __construct()
	{
		$this->writers = new PriorityQueue();
	}

	public function addWriter(WriterInterface $writer, $priority = 0)
	{
		$this->writers->insert($writer, $priority);
	}

	public function getDefaultWriter(array $supportedWriter = null)
	{
		foreach($this->writers as $writer)
		{
			$className = get_class($writer);

			if($supportedWriter !== null && !in_array($className, $supportedWriter))
			{
				continue;
			}

			return $writer;
		}

		return null;
	}

	public function getWriterByContentType($contentType, array $supportedWriter = null)
	{
		if(empty($contentType))
		{
			return null;
		}

		$contentTypes = MediaType::parseList($contentType);

		// first we check all custom content negotiation rules
		if(!empty($this->contentNegotiation))
		{
			foreach($contentTypes as $contentType)
			{
				$writer = $this->getWriterFromContentNegotiation($contentType, $supportedWriter);

				if($writer !== null)
				{
					return $writer;
				}
			}
		}

		// then we ask every writer whether they support the content type
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

	public function getWriterByFormat($format, array $supportedWriter = null)
	{
		foreach($this->writers as $writer)
		{
			$className = get_class($writer);

			if($supportedWriter !== null && !in_array($className, $supportedWriter))
			{
				continue;
			}

			$pos       = strrpos($className, '\\');
			$shortName = strtolower(substr($className, $pos === false ? 0 : $pos + 1));

			if($shortName == $format)
			{
				return $writer;
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

	/**
	 * With this method you can set which writer should be used for an specific
	 * content type. The content type can be i.e. text/plain or image/*
	 *
	 * @param string $contentType
	 * @param string $writerClass
	 */
	public function setContentNegotiation($contentType, $writerClass)
	{
		$this->contentNegotiation[$contentType] = $writerClass;
	}

	/**
	 * Returns the fitting writer according to the content negotiation. If no
	 * fitting writer could be found null gets returned
	 *
	 * @return PSX\Data\WriterInterface
	 */
	protected function getWriterFromContentNegotiation(MediaType $contentType, array $supportedWriter = null)
	{
		if(empty($this->contentNegotiation))
		{
			return null;
		}

		foreach($this->contentNegotiation as $acceptedContentType => $writerClass)
		{
			if($supportedWriter !== null && !in_array($writerClass, $supportedWriter))
			{
				continue;
			}

			$acceptedContentType = MediaType::parse($acceptedContentType);

			if($acceptedContentType->match($contentType))
			{
				$writer = $this->getWriterByInstance($writerClass);

				if($writer !== null)
				{
					return $writer;
				}
			}
		}

		return null;
	}
}

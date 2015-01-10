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

use Psr\Http\Message\MessageInterface;
use PSX\Data\ReaderFactory;
use PSX\Data\TransformerInterface;
use PSX\Data\Transformer\TransformerManager;
use PSX\Http\Exception\UnsupportedMediaTypeException;

/**
 * Extractor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Extractor
{
	/**
	 * @var PSX\Data\ReaderFactory
	 */
	protected $readerFactory;

	/**
	 * @var PSX\Data\Record\TransformerManager
	 */
	protected $transformerManager;

	public function __construct(ReaderFactory $readerFactory, TransformerManager $transformerManager)
	{
		$this->readerFactory      = $readerFactory;
		$this->transformerManager = $transformerManager;
	}

	/**
	 * Extracts the body from an http message and transforms the data into an 
	 * array structure
	 *
	 * @param Psr\Http\Message\MessageInterface $message
	 * @param PSX\Data\Record\TransformerInterface $transformer
	 * @param string $readerType
	 * @return array
	 */
	public function extract(MessageInterface $message, TransformerInterface $transformer = null, $readerType = null)
	{
		$contentType = strstr($message->getHeader('Content-Type') . ';', ';', true);
		$reader      = $this->getRequestReader($contentType, $readerType);
		$data        = $reader->read($message);

		// get transformer
		if($transformer === null)
		{
			$transformer = $this->transformerManager->getTransformerByContentType($contentType);
		}

		if($transformer instanceof TransformerInterface)
		{
			$data = $transformer->transform($data);
		}

		return $data;
	}

	protected function getRequestReader($contentType, $readerType)
	{
		// find best reader type
		if($readerType === null)
		{
			$reader = $this->readerFactory->getReaderByContentType($contentType);
		}
		else
		{
			$reader = $this->readerFactory->getReaderByInstance($readerType);
		}

		if($reader === null)
		{
			$reader = $this->readerFactory->getDefaultReader();

			// @TODO the correct response would be to throw an unsupported 
			// content type exception since this would enforce clients to send
			// an correct content type
			//throw new UnsupportedMediaTypeException('Unsupported content type', 415);
		}

		return $reader;
	}
}

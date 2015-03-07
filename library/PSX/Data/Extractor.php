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

namespace PSX\Data;

use PSX\Data\ReaderFactory;
use PSX\Data\TransformerInterface;
use PSX\Data\Transformer\TransformerManager;
use PSX\Http\Exception\UnsupportedMediaTypeException;
use PSX\Http\MessageInterface;

/**
 * Extractor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
	 * @param PSX\Http\MessageInterface $message
	 * @param PSX\Data\Record\TransformerInterface $transformer
	 * @param string $readerType
	 * @return array
	 */
	public function extract(MessageInterface $message, TransformerInterface $transformer = null, $readerType = null)
	{
		$contentType = $message->getHeader('Content-Type');
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

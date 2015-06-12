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

use PSX\Data\NotFoundException;
use PSX\Data\ReaderFactory;
use PSX\Data\Record\ImporterManager;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\TransformerInterface;
use PSX\Data\Transformer\TransformerManager;
use PSX\Http\MessageInterface;
use RuntimeException;

/**
 * Reads data from an http message and imports them into an record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Importer
{
	/**
	 * @var PSX\Data\Extractor
	 */
	protected $extractor;

	/**
	 * @var PSX\Data\Record\ImporterManager
	 */
	protected $importerManager;

	public function __construct(Extractor $extractor, ImporterManager $importerManager)
	{
		$this->extractor       = $extractor;
		$this->importerManager = $importerManager;
	}

	/**
	 * Imports data from an http message into an record. The reader which gets 
	 * used depends on the content type. If not other specified a transformer 
	 * for the content type gets loaded. If no transformer is available we 
	 * simply pass the data from the reader to the importer
	 *
	 * @param mixed $source
	 * @param PSX\Http\MessageInterface $message
	 * @param PSX\Data\Record\TransformerInterface $transformer
	 * @param string $readerType
	 * @return PSX\Data\RecordInterface
	 */
	public function import($source, MessageInterface $message, TransformerInterface $transformer = null, $readerType = null)
	{
		$data = $this->extractor->extract($message, $transformer, $readerType);

		if(is_callable($source))
		{
			$source = call_user_func_array($source, array($data));
		}

		$importer = $this->importerManager->getImporterBySource($source);

		if($importer instanceof ImporterInterface)
		{
			if($data === null)
			{
				throw new RuntimeException('No data available');
			}

			return $importer->import($source, $data);
		}
		else
		{
			throw new NotFoundException('Could not find fitting importer');
		}
	}
}

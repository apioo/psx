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

use PSX\Data\NotFoundException;
use PSX\Data\ReaderFactory;
use PSX\Data\Record\ImporterManager;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\TransformerInterface;
use PSX\Data\Transformer\TransformerManager;
use PSX\Http\MessageInterface;

/**
 * Reads data from an http message and imports them into an record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
			return $importer->import($source, $data);
		}
		else
		{
			throw new NotFoundException('Could not find fitting importer');
		}
	}
}

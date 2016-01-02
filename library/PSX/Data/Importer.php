<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Data\Record\ImporterInterface;
use PSX\Data\Record\ImporterManager;
use PSX\Http\MessageInterface;
use RuntimeException;

/**
 * Reads data from a http message and imports them into a record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Importer
{
    /**
     * @var \PSX\Data\Extractor
     */
    protected $extractor;

    /**
     * @var \PSX\Data\Record\ImporterManager
     */
    protected $importerManager;

    public function __construct(Extractor $extractor, ImporterManager $importerManager)
    {
        $this->extractor       = $extractor;
        $this->importerManager = $importerManager;
    }

    /**
     * Imports data from a http message into a record. The reader which gets
     * used depends on the content type. If not other specified a transformer
     * for the content type gets loaded. If no transformer is available we
     * simply pass the data from the reader to the importer. If no importer was
     * explicit provided the importer is determined based on the source
     *
     * @param mixed $source
     * @param \PSX\Http\MessageInterface $message
     * @param \PSX\Data\TransformerInterface $transformer
     * @param string $readerType
     * @param \PSX\Data\Record\ImporterInterface $importer
     * @return \PSX\Data\RecordInterface
     */
    public function import($source, MessageInterface $message, TransformerInterface $transformer = null, $readerType = null, ImporterInterface $importer = null)
    {
        $data = $this->extractor->extract($message, $transformer, $readerType);

        if (is_callable($source)) {
            $source = call_user_func_array($source, array($data));
        }

        if ($importer === null) {
            $importer = $this->createBySource($source);
        }

        if ($importer instanceof ImporterInterface) {
            if ($data === null) {
                throw new RuntimeException('No data available');
            }

            return $importer->import($source, $data);
        } else {
            throw new NotFoundException('Could not find fitting importer');
        }
    }

    /**
     * Returns an importer which understands the provided source
     *
     * @param mixed $source
     * @return \PSX\Data\Record\ImporterInterface
     */
    public function createBySource($source)
    {
        return $this->importerManager->getImporterBySource($source);
    }
}

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

namespace PSX\Controller\Behaviour;

use PSX\Data\TransformerInterface;

/**
 * Provides an method to import data from an request into an source
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
trait ImporterTrait
{
	/**
	 * @Inject
	 * @var PSX\Data\Importer
	 */
	protected $importer;

	/**
	 * Imports data from the current request into an record
	 *
	 * @param mixed $source
	 * @param PSX\Data\TransformerInterface $transformer
	 * @param string $readerType
	 * @return PSX\Data\RecordInterface
	 */
	protected function import($source, TransformerInterface $transformer = null, $readerType = null)
	{
		return $this->importer->import($source, $this->request, $transformer, $readerType);
	}
}

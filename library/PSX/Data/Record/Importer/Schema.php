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

namespace PSX\Data\Record\Importer;

use InvalidArgumentException;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Assimilator;
use PSX\Data\Schema\SchemaTraverser;
use PSX\Data\Schema\Visitor\AssimilationVisitor;
use PSX\Data\Schema\ValidatorInterface;

/**
 * Imports data based on an given schema. Validates also the data if an 
 * validator was set
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Schema implements ImporterInterface
{
	protected $assimilator;

	public function __construct(Assimilator $assimilator)
	{
		$this->assimilator = $assimilator;
	}

	public function accept($schema)
	{
		return $schema instanceof SchemaInterface;
	}

	public function import($schema, $data)
	{
		if(!$schema instanceof SchemaInterface)
		{
			throw new InvalidArgumentException('Schema must be an instanceof PSX\Data\SchemaInterface');
		}

		if(!$data instanceof \stdClass)
		{
			throw new InvalidArgumentException('Data must be an stdClass');
		}

		return $this->assimilator->assimilate($schema, $data, true, SchemaTraverser::TYPE_INCOMING);
	}
}

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

namespace PSX\Data\Schema;

use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Time;

/**
 * Assimilator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Assimilator
{
	protected $factory;
	protected $traverser;

	public function __construct(FactoryFactory $factory)
	{
		$this->factory   = $factory;
		$this->traverser = new SchemaTraverser();
	}

	/**
	 * Takes an array and fits it accoring to the specification. If validate is 
	 * true all values are also validated
	 *
	 * @param array $data
	 * @param PSX\Data\SchemaInterface $schema
	 * @param boolean $validate
	 * @param integer $type
	 * @return array
	 */
	public function assimilate(SchemaInterface $schema, $data, $validate = false, $type = SchemaTraverser::TYPE_OUTGOING)
	{
		return $this->traverser->traverse($data, $schema, new Visitor\AssimilationVisitor($validate, $this->factory), $type);
	}
}

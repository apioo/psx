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

namespace PSX\Data\Record\Importer\Test;

use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;

/**
 * Schema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Schema extends SchemaAbstract
{
	public function getDefinition()
	{
		$sb = $this->getSchemaBuilder('person');
		$sb->setReference('PSX\Data\Record\Importer\Test\Person');
		$sb->string('title')->setRequired(true);
		$person = $sb->getProperty();

		$sb = $this->getSchemaBuilder('entry');
		$sb->setReference('PSX\Data\Record\Importer\Test\Entry');
		$sb->string('title')->setRequired(true);
		$entry = $sb->getProperty();

		$sb = $this->getSchemaBuilder('token');
		$sb->setReference('PSX\Data\Record\Importer\Test\Factory');
		$sb->string('alg')->setRequired(true);
		$sb->string('sig')->setRequired(true);
		$token = $sb->getProperty();

		$sb = $this->getSchemaBuilder('news');
		$sb->integer('id')->setRequired(true);
		$sb->string('title')->setPattern('[A-z]{3,16}')->setRequired(true);
		$sb->boolean('active')->setRequired(true);
		$sb->boolean('disabled')->setRequired(true);
		$sb->integer('count')->setEnumeration(array(6, 12))->setRequired(true);
		$sb->float('rating')->setMin(8)->setMax(14)->setRequired(true);
		$sb->dateTime('date')->setRequired(true);
		$sb->complexType($person);
		$sb->arrayType('tags')->setPrototype(new Property\String('tag'))->setMinLength(0)->setMaxLength(4);
		$sb->arrayType('entry')->setPrototype($entry);
		$sb->complexType($token);
		$sb->string('url')->setReference('PSX\Url')->setRequired(true);

		return $sb->getProperty();
	}
}

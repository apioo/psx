<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Record\Importer;

use PSX\Data\RecordAbstract;
use PSX\Data\Record\ImporterTestCase;
use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;

/**
 * SchemaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SchemaTest extends \PHPUnit_Framework_TestCase
{
	use ImporterTestCase;
	
	protected function getImporter()
	{
		return new Schema(getContainer()->get('schema_validator'));
	}

	protected function getRecord()
	{
		return getContainer()->get('schema_manager')->getSchema('PSX\Data\Record\Importer\DefaultSchema');
	}
}

class DefaultSchema extends SchemaAbstract
{
	public function getDefinition()
	{
		$sb = $this->getSchemaBuilder('person');
		$sb->string('title');
		$person = $sb->getProperty();

		$sb = $this->getSchemaBuilder('entry');
		$sb->string('title');
		$entry = $sb->getProperty();

		$sb = $this->getSchemaBuilder('news');
		$sb->integer('id');
		$sb->string('title');
		$sb->boolean('active');
		$sb->boolean('disabled');
		$sb->integer('count');
		$sb->float('rating');
		$sb->dateTime('date');
		$sb->complexType($person);
		$sb->arrayType('tags')->setPrototype(new Property\String('tag'));
		$sb->arrayType('entry')->setPrototype($entry);

		return $sb->getProperty();
	}
}

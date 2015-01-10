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

namespace PSX\Data\Record\Importer\Test;

use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;

/**
 * Schema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

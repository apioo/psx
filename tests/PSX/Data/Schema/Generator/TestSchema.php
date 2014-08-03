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

namespace PSX\Data\Schema\Generator;

use PSX\Data\SchemaAbstract;
use PSX\Data\Schema\Property;

/**
 * TestSchema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestSchema extends SchemaAbstract
{
	public function getDefinition()
	{
		$sb = $this->getSchemaBuilder('author')
			->setDescription('An simple author element with some description');
		$sb->string('title')
			->setPattern('[A-z]{3,16}')
			->setRequired(true);
		$sb->string('email')
			->setDescription('We will send no spam to this addresss');
		$author = $sb->getProperty();

		$sb = $this->getSchemaBuilder('news')
			->setDescription('An general news entry');
		$sb->arrayType('tags')
			->setPrototype(new Property\String('tag'))
			->setMinLength(1);
		$sb->arrayType('receiver')
			->setPrototype($author)
			->setMinLength(1)
			->setRequired(true);
		$sb->boolean('read');
		$sb->complexType($author)
			->setRequired(true);
		$sb->date('sendDate');
		$sb->dateTime('readDate');
		$sb->duration('expires');
		$sb->float('price')
			->setMin(1)
			->setMax(100)
			->setRequired(true);
		$sb->integer('rating')
			->setMin(1)
			->setMax(5);
		$sb->string('content')
			->setDescription('Contains the main content of the news entry')
			->setMinLength(3)
			->setMaxLength(512)
			->setRequired(true);
		$sb->string('question')
			->setEnumeration(array('foo', 'bar'));
		$sb->time('coffeeTime');

		return $sb->getProperty();
	}
}

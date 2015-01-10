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

use Doctrine\Common\Annotations\AnnotationRegistry;
use PSX\Api\Version;

/**
 * SerializerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
	public function testSerialize()
	{
		$object = new Serializer\TestObject();
		$object->setName('foo');
		$object->setAuthor('bar');

		$return = getContainer()->get('serializer')->serialize($object);

		$this->assertEquals(array('name' => 'foo', 'author' => 'bar'), $return);
	}

	public function testSerializeVersioned()
	{
		$object = new Serializer\TestObjectVersioned();
		$object->setName('foo');
		$object->setAuthor('bar');

		$return = getContainer()->get('serializer')->serialize($object, new Version(1));

		$this->assertEquals(array('name' => 'foo'), $return);
	}

	public function testSerializeVersionedGreater()
	{
		$object = new Serializer\TestObjectVersioned();
		$object->setName('foo');
		$object->setAuthor('bar');

		$return = getContainer()->get('serializer')->serialize($object, new Version(2));

		$this->assertEquals(array('name' => 'foo', 'author' => 'bar'), $return);
	}
}

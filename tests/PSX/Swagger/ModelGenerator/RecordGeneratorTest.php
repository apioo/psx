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

namespace PSX\Swagger\ModelGenerator;

use PSX\Handler\Database\TestRecord;
use PSX\Swagger\Property;

/**
 * RecordGeneratorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RecordGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testGetComplexType()
	{
		$testRecord = new TestRecord();
		$generator  = new RecordGenerator();
		$model      = $generator->getComplexType($testRecord);

		$this->assertEquals('testRecord', $model->getId());

		$properties = $model->getProperties();

		$this->assertEquals('id', $properties['id']->getId());
		$this->assertEquals(Property::TYPE_INTEGER, $properties['id']->getType());

		$this->assertEquals('userId', $properties['userId']->getId());
		$this->assertEquals(Property::TYPE_INTEGER, $properties['userId']->getType());

		$this->assertEquals('date', $properties['date']->getId());
		$this->assertEquals(Property::TYPE_STRING, $properties['date']->getType());
	}
}

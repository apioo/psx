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

namespace PSX\Validate;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Filter;
use PSX\Validate;
use PSX\Validate\Property;

/**
 * ValidateAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ValidateAbstractTest extends \PHPUnit_Framework_TestCase
{
	public function testGetRecord()
	{
		$validator = new ArrayValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER),
			new Property('title', Validate::TYPE_STRING, array(new Filter\Length(1, 2))),
		));

		$this->assertInstanceOf('PSX\Data\RecordInterface', $validator->getRecord());
		$this->assertEquals(array('id' => null, 'title' => null), $validator->getRecord()->getRecordInfo()->getFields());
	}
}
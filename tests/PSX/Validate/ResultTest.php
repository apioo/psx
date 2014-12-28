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
 * ResultTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
	public function testResult()
	{
		$result = new Result('foo', array('An error occured'));

		$this->assertEquals('foo', $result->getValue());
		$this->assertEquals('An error occured', $result->getFirstError());

		$result->setValue('bar');
		$result->setErrors(array('Another error'));
		$result->addError('More errors');

		$this->assertEquals('bar', $result->getValue());
		$this->assertEquals('Another error', $result->getFirstError());
		$this->assertFalse($result->isSuccessful());
		$this->assertTrue($result->hasError());
		$this->assertEquals('Another error, More errors', $result->__toString());
	}
}

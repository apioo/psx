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

namespace PSX\Api\View\Generator\Wsdl;

/**
 * OperationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OperationTest extends \PHPUnit_Framework_TestCase
{
	public function testIn()
	{
		$operation = new Operation('getEntry');
		$operation->setMethod('GET');
		$operation->setIn('void');
		$operation->setOut('collection');

		$this->assertEquals('getEntry', $operation->getName());
		$this->assertEquals('GET', $operation->getMethod());
		$this->assertEquals('void', $operation->getIn());
		$this->assertTrue($operation->hasIn());
		$this->assertEquals('collection', $operation->getOut());
		$this->assertTrue($operation->hasOut());

		$this->assertTrue($operation->hasOperation());
		$this->assertFalse($operation->isInOnly());
		$this->assertFalse($operation->isOutOnly());
		$this->assertTrue($operation->isInOut());
	}
}

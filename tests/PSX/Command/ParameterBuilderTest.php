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

namespace PSX\Command;

use PSX\Command\Executor;
use PSX\Command\ParameterParser\Map;
use PSX\Command\Output\Memory;

/**
 * ParameterBuilderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ParameterBuilderTest extends \PHPUnit_Framework_TestCase
{
	public function testBuild()
	{
		$builder    = new ParameterBuilder();
		$parameters = $builder->setDescription('foobar')
			->addOption('bar', Parameter::TYPE_REQUIRED, 'bar description')
			->addOption('foo', Parameter::TYPE_OPTIONAL, 'foo description')
			->addOption('v', Parameter::TYPE_FLAG, 'verbose')
			->getParameters();

		$this->assertInstanceOf('PSX\Command\Parameters', $parameters);
		$this->assertEquals('foobar', $parameters->getDescription());
		$this->assertEquals(3, count($parameters));
		$this->assertEquals(null, $parameters->get('unknown'));

		$this->assertInstanceOf('PSX\Command\Parameter', $parameters[0]);
		$this->assertEquals('bar', $parameters[0]->getName());
		$this->assertEquals(Parameter::TYPE_REQUIRED, $parameters[0]->getType());
		$this->assertEquals('bar description', $parameters[0]->getDescription());
		$this->assertEquals(false, $parameters[0]->hasValue());
		$this->assertEquals(null, $parameters[0]->getValue());

		$this->assertInstanceOf('PSX\Command\Parameter', $parameters[1]);
		$this->assertEquals('foo', $parameters[1]->getName());
		$this->assertEquals(Parameter::TYPE_OPTIONAL, $parameters[1]->getType());
		$this->assertEquals('foo description', $parameters[1]->getDescription());
		$this->assertEquals(false, $parameters[1]->hasValue());
		$this->assertEquals(null, $parameters[1]->getValue());

		$this->assertInstanceOf('PSX\Command\Parameter', $parameters[2]);
		$this->assertEquals('v', $parameters[2]->getName());
		$this->assertEquals(Parameter::TYPE_FLAG, $parameters[2]->getType());
		$this->assertEquals('verbose', $parameters[2]->getDescription());
		$this->assertEquals(false, $parameters[2]->hasValue());
		$this->assertEquals(null, $parameters[2]->getValue());

		foreach($parameters as $parameter)
		{
			$this->assertInstanceOf('PSX\Command\Parameter', $parameter);
		}
	}
}

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

/**
 * ParameterParserTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ParameterParserTestCase extends \PHPUnit_Framework_TestCase
{
	public function testFillParameters()
	{
		$builder = new ParameterBuilder();
		$builder->addOption('foo', Parameter::TYPE_REQUIRED);
		$builder->addOption('bar', Parameter::TYPE_OPTIONAL);
		$builder->addOption('flag', Parameter::TYPE_FLAG);

		$parser     = $this->getParameterParser();
		$parameters = $builder->getParameters();

		$this->assertEquals('Foo\Bar', $parser->getClassName());

		$this->assertEquals(null, $parameters->get('foo'));
		$this->assertEquals(false, $parameters->has('foo'));
		$this->assertEquals(null, $parameters->get('bar'));
		$this->assertEquals(false, $parameters->has('bar'));
		$this->assertEquals(null, $parameters->get('flag'));
		$this->assertEquals(false, $parameters->has('flag'));

		$parser->fillParameters($parameters);

		$this->assertEquals('bar', $parameters->get('foo'));
		$this->assertEquals(true, $parameters->has('foo'));
		$this->assertEquals('foo', $parameters->get('bar'));
		$this->assertEquals(true, $parameters->has('bar'));
		$this->assertEquals(true, $parameters->get('flag'));
		$this->assertEquals(true, $parameters->has('flag'));
	}

	public function testFillParametersOptional()
	{
		$builder = new ParameterBuilder();
		$builder->addOption('bar', Parameter::TYPE_OPTIONAL);
		$builder->addOption('test', Parameter::TYPE_OPTIONAL);

		$parser     = $this->getParameterParser();
		$parameters = $builder->getParameters();

		$this->assertEquals(null, $parameters->get('bar'));
		$this->assertEquals(false, $parameters->has('bar'));
		$this->assertEquals(null, $parameters->get('test'));
		$this->assertEquals(false, $parameters->has('test'));

		$parser->fillParameters($parameters);

		$this->assertEquals('foo', $parameters->get('bar'));
		$this->assertEquals(true, $parameters->has('bar'));
		$this->assertEquals(null, $parameters->get('test'));
		$this->assertEquals(false, $parameters->has('test'));
	}

	/**
	 * @expectedException PSX\Command\MissingParameterException
	 */
	public function testFillParametersRequired()
	{
		$builder = new ParameterBuilder();
		$builder->addOption('bar', Parameter::TYPE_REQUIRED);
		$builder->addOption('test', Parameter::TYPE_REQUIRED);

		$parser     = $this->getParameterParser();
		$parameters = $builder->getParameters();

		$this->assertEquals(null, $parameters->get('bar'));
		$this->assertEquals(false, $parameters->has('bar'));
		$this->assertEquals(null, $parameters->get('test'));
		$this->assertEquals(false, $parameters->has('test'));

		$parser->fillParameters($parameters);
	}

	public function testFillParametersFlag()
	{
		$builder = new ParameterBuilder();
		$builder->addOption('bar', Parameter::TYPE_FLAG);
		$builder->addOption('test', Parameter::TYPE_FLAG);

		$parser     = $this->getParameterParser();
		$parameters = $builder->getParameters();

		$this->assertEquals(null, $parameters->get('bar'));
		$this->assertEquals(false, $parameters->has('bar'));
		$this->assertEquals(null, $parameters->get('test'));
		$this->assertEquals(false, $parameters->has('test'));

		$parser->fillParameters($parameters);

		$this->assertEquals(true, $parameters->get('bar'));
		$this->assertEquals(true, $parameters->has('bar'));
		$this->assertEquals(false, $parameters->get('test'));
		$this->assertEquals(false, $parameters->has('test'));
	}

	abstract protected function getParameterParser();
}

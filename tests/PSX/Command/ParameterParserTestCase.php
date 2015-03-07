<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Command;

/**
 * ParameterParserTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

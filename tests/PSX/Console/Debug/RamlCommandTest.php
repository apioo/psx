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

namespace PSX\Console\Debug;

use PSX\Test\CommandTestCase;
use PSX\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * RamlCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlCommandTest extends CommandTestCase
{
	public function testCommand()
	{
		$command = new RamlCommand();

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
			'file'   => __DIR__ . '/../../Api/Documentation/Parser/test.raml',
			'path'   => '/foo',
			'format' => 'serialize',
		));

		$documentation = unserialize($commandTester->getDisplay());

		$this->assertInstanceOf('PSX\Api\DocumentationInterface', $documentation);

		$resource = $documentation->getResource($documentation->getLatestVersion());

		$this->assertInstanceOf('PSX\Api\Resource', $resource);
		$this->assertEquals(['GET', 'POST'], $resource->getAllowedMethods());
	}

	public function testCommandAvailable()
	{
		$command = Environment::getService('console')->find('debug:raml');

		$this->assertInstanceOf('PSX\Console\Debug\RamlCommand', $command);
	}
}

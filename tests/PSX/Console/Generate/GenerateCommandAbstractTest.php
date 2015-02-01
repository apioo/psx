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

namespace PSX\Console\Generate;

use PSX\Test\CommandTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * GenerateCommandAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GenerateCommandAbstractTest extends \PHPUnit_Framework_TestCase
{
	public function testGetServiceDefinition()
	{
		$command    = new FooGenerateCommand('foo');
		$definition = new InputDefinition(array(
			new InputArgument('namespace', InputArgument::REQUIRED), 
			new InputOption('dry-run', null, InputOption::VALUE_NONE)
		));

		$input      = new ArrayInput(array('namespace' => 'Foo\Bar\Test', '--dry-run' => false), $definition);
		$definition = $command->testGetServiceDefinition($input);

		$this->assertInstanceOf('PSX\Console\Generate\ServiceDefinition', $definition);
		$this->assertEquals('Foo\Bar', $definition->getNamespace());
		$this->assertEquals('Test', $definition->getClassName());
		$this->assertFalse($definition->isDryRun());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetServiceDefinitionNoNamespace()
	{
		$command    = new FooGenerateCommand('foo');
		$definition = new InputDefinition(array(
			new InputArgument('namespace', InputArgument::REQUIRED), 
			new InputOption('dry-run', null, InputOption::VALUE_NONE)
		));

		$input      = new ArrayInput(array('namespace' => 'Test', '--dry-run' => false), $definition);
		$definition = $command->testGetServiceDefinition($input);
	}

	public function testMakeDir()
	{
		$path = PSX_PATH_CACHE . '/command_test';

		if(is_dir($path))
		{
			rmdir($path);
		}

		$command = new FooGenerateCommand('foo');
		$command->testMakeDir($path);

		$this->assertTrue(is_dir($path));
		$this->assertTrue($command->testIsDir($path));
	}

	public function testWriteFile()
	{
		$path = PSX_PATH_CACHE . '/command_test.txt';

		if(is_file($path))
		{
			unlink($path);
		}

		$command = new FooGenerateCommand('foo');
		$command->testWriteFile($path, 'foobar');

		$this->assertTrue(is_file($path));
		$this->assertEquals('foobar', file_get_contents($path));
		$this->assertTrue($command->testIsFile($path));
	}
}

class FooGenerateCommand extends GenerateCommandAbstract
{
	public function testGetServiceDefinition(InputInterface $input)
	{
		return $this->getServiceDefinition($input);
	}

	public function testMakeDir($path)
	{
		return $this->makeDir($path);
	}

	public function testWriteFile($file, $content)
	{
		return $this->writeFile($file, $content);
	}

	public function testIsFile($path)
	{
		return $this->isFile($path);
	}

	public function testIsDir($path)
	{
		return $this->isDir($path);
	}
}

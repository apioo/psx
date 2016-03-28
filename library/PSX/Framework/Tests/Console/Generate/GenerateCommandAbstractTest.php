<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Tests\Console\Generate;

use PSX\Framework\Console\Generate\GenerateCommandAbstract;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * GenerateCommandAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

        $this->assertInstanceOf('PSX\Framework\Console\Generate\ServiceDefinition', $definition);
        $this->assertEquals('Foo\Bar', $definition->getNamespace());
        $this->assertEquals('Test', $definition->getClassName());
        $this->assertFalse($definition->isDryRun());
    }

    /**
     * @expectedException \InvalidArgumentException
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

        if (is_dir($path)) {
            rmdir($path);
        }

        $command = new FooGenerateCommand('foo');
        $command->testMakeDir($path);

        $this->assertTrue(is_dir($path));
        $this->assertTrue($command->testIsDir($path));
    }

    public function testWriteFile()
    {
        $path = PSX_PATH_CACHE . '/CommandTest.txt';

        if (is_file($path)) {
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

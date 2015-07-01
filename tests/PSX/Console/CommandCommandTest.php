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

namespace PSX\Console;

use PSX\Command\Output;
use PSX\Test\CommandTestCase;
use PSX\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CommandCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CommandCommandTest extends CommandTestCase
{
    public function testCommandParameter()
    {
        $memory = new Output\Memory();

        Environment::getContainer()->set('command_output', $memory);

        $command = Environment::getService('console')->find('command');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'cmd'        => 'PSX\Command\Foo\Command\FooCommand',
            'parameters' => array('foo:test'),
        ));

        $messages = $memory->getMessages();

        $this->assertEquals(2, count($messages));
        $this->assertEquals('Some foo informations', rtrim($messages[0]));
        $this->assertEquals('Hello test', rtrim($messages[1]));
    }

    /**
     * @expectedException \PSX\Command\MissingParameterException
     */
    public function testCommandParameterEmpty()
    {
        $memory = new Output\Memory();

        Environment::getContainer()->set('command_output', $memory);

        $command = Environment::getService('console')->find('command');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'cmd'        => 'PSX\Command\Foo\Command\FooCommand',
            'parameters' => array(),
        ));
    }

    public function testCommandStdin()
    {
        $memory = new Output\Memory();

        Environment::getContainer()->set('command_output', $memory);

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, '{"foo": "test"}');
        rewind($stream);

        $command = Environment::getService('console')->find('command');
        $command->setReader(new Reader\Stdin($stream));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'cmd'     => 'PSX\Command\Foo\Command\FooCommand',
            '--stdin' => true,
        ));

        $messages = $memory->getMessages();

        $this->assertEquals(2, count($messages));
        $this->assertEquals('Some foo informations', rtrim($messages[0]));
        $this->assertEquals('Hello test', rtrim($messages[1]));
    }

    /**
     * @expectedException \PSX\Command\MissingParameterException
     */
    public function testCommandStdinEmptyBody()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, '');
        rewind($stream);

        $command = Environment::getService('console')->find('command');
        $command->setReader(new Reader\Stdin($stream));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'cmd'     => 'PSX\Command\Foo\Command\FooCommand',
            '--stdin' => true,
        ));
    }

    /**
     * @expectedException \PSX\Exception
     */
    public function testCommandStdinInvalidJson()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'foobar');
        rewind($stream);

        $command = Environment::getService('console')->find('command');
        $command->setReader(new Reader\Stdin($stream));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'cmd'     => 'PSX\Command\Foo\Command\FooCommand',
            '--stdin' => true,
        ));
    }
}

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

namespace PSX\Framework\Tests\Console;

use PSX\Framework\Console\Reader\Stdin;
use PSX\Framework\Dispatch\Sender\Memory;
use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ServeCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ServeCommandTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        // use memory sender
        Environment::getContainer()->set('dispatch_sender', new Memory());
    }

    public function testCommand()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, 'GET /api HTTP/1.1' . "\n" . 'Accept: application/xml' . "\n" . "\x04");
        rewind($stream);

        $command = Environment::getService('console')->find('serve');
        $command->setReader(new Stdin($stream));

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
        ));

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<foo>
 <bar>foo</bar>
</foo>
XML;

        $this->assertXmlStringEqualsXmlString($expect, Environment::getService('dispatch_sender')->getResponse());
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/api', 'PSX\Framework\Tests\Controller\Foo\Application\TestApiController::doIndex'],
        );
    }
}

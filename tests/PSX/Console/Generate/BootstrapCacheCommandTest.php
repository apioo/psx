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

namespace PSX\Console\Generate;

use PSX\Test\Assert;
use PSX\Test\CommandTestCase;
use PSX\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * BootstrapCacheCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BootstrapCacheCommandTest extends CommandTestCase
{
    public function testCommand()
    {
        $command = $this->getMockBuilder('PSX\Console\Generate\BootstrapCacheCommand')
            ->setMethods(array('makeDir', 'writeFile', 'getFiles'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('getFiles')
            ->will($this->returnValue(array(
                __DIR__ . '/BootstrapCache/Bar.php',
                __DIR__ . '/BootstrapCache/Foo.php',
            )));

        $command->expects($this->at(1))
            ->method('writeFile')
            ->with(
                $this->equalTo('cache/bootstrap.cache.php'),
                $this->callback(function ($source) {
                    Assert::assertStringMatchIgnoreWhitespace($this->getExpectedSource(), $source);
                    return true;
                })
            );

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
        ));
    }

    public function testCommandAvailable()
    {
        $command = Environment::getService('console')->find('generate:bootstrap_cache');

        $this->assertInstanceOf('PSX\Console\Generate\BootstrapCacheCommand', $command);
    }

    protected function getExpectedSource()
    {
        $src = '<?php' . "\n";
        $src.= 'namespace PSX\Console\Generate\BootstrapCache { class Bar { public function __construct() { } } }' . "\n";
        $src.= 'namespace PSX\Console\Generate\BootstrapCache { class Foo { protected function doBar($value) { } } }' . "\n";

        return $src;
    }
}

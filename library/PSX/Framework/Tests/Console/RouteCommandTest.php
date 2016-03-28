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

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * RouteCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RouteCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = Environment::getService('console')->find('route');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
        ));

        $collection = Environment::getService('routing_parser')->getCollection();
        $response   = $commandTester->getDisplay();

        foreach ($collection as $route) {
            $methods = implode('|', $route[0]);

            $this->assertTrue(strpos($response, $methods) !== false, $methods);
            $this->assertTrue(strpos($response, $route[1]) !== false, $route[1]);
            $this->assertTrue(strpos($response, $route[2]) !== false, $route[2]);
        }
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/controller', 'PSX\Framework\Tests\Controller\Foo\Application\TestController::doIndex'],
        );
    }
}

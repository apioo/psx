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

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ControllerCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\ControllerCommand')
            ->setConstructorArgs(array(Environment::getContainer()))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

        $command->expects($this->at(2))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(3))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'),
                $this->callback(function ($source) {
                    $this->assertSource($this->getExpectedSource(), $source);
                    return true;
                })
            );

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'namespace' => 'Acme\Foo\Bar',
            'services'  => 'connection,template'
        ));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCommandFileExists()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\ControllerCommand')
            ->setConstructorArgs(array(Environment::getContainer()))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo'));

        $command->expects($this->at(2))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(true));

        $command->expects($this->never())
            ->method('writeFile');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'namespace' => 'Acme\Foo\Bar',
            'services'  => 'connection,template'
        ));
    }

    public function testCommandAvailable()
    {
        $command = Environment::getService('console')->find('generate:controller');

        $this->assertInstanceOf('PSX\Framework\Console\Generate\ControllerCommand', $command);
    }

    protected function assertSource($expect, $actual)
    {
        $expect = str_replace(array("\r\n", "\n", "\r"), "\n", $expect);
        $actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);

        $this->assertEquals($expect, $actual);
    }

    protected function getExpectedSource()
    {
        return <<<'PHP'
<?php

namespace Acme\Foo;

use PSX\Framework\Controller\ControllerAbstract;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class Bar extends ControllerAbstract
{
	/**
	 * @Inject
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @Inject
	 * @var \PSX\Framework\Template\TemplateInterface
	 */
	protected $template;

	public function doIndex()
	{
		// @TODO controller action

		$this->setBody(array(
			'message' => 'This is the default controller of PSX',
		));
	}
}

PHP;
    }
}

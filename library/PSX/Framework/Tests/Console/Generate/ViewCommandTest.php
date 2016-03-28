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
 * ViewCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ViewCommandTest extends ControllerTestCase
{
    public function testCommand()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\ViewCommand')
            ->setConstructorArgs(array(Environment::getContainer()))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'));

        $command->expects($this->at(2))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'))
            ->will($this->returnValue(false));

        $command->expects($this->at(3))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'));

        $command->expects($this->at(4))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(5))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'bar.html'))
            ->will($this->returnValue(false));

        $command->expects($this->at(6))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'),
                $this->callback(function ($source) {
                    $this->assertSource($this->getExpectedControllerSource(), $source);
                    return true;
                })
            );

        $command->expects($this->at(7))
            ->method('writeFile')
            ->with(
                $this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'bar.html'),
                $this->callback(function ($source) {
                    $this->assertSource($this->getExpectedResourceSource(), $source);
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
    public function testCommandControllerFileExists()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\ViewCommand')
            ->setConstructorArgs(array(Environment::getContainer()))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'));

        $command->expects($this->at(2))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'))
            ->will($this->returnValue(false));

        $command->expects($this->at(3))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'));

        $command->expects($this->at(4))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(true));

        $command->expects($this->at(5))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(true));

        $command->expects($this->never())
            ->method('writeFile');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'namespace' => 'Acme\Foo\Bar',
            'services'  => 'connection,template'
        ));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCommandTemplateFileExists()
    {
        $command = $this->getMockBuilder('PSX\Framework\Console\Generate\ViewCommand')
            ->setConstructorArgs(array(Environment::getContainer()))
            ->setMethods(array('makeDir', 'writeFile', 'isDir', 'isFile'))
            ->getMock();

        $command->expects($this->at(0))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'))
            ->will($this->returnValue(false));

        $command->expects($this->at(1))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'));

        $command->expects($this->at(2))
            ->method('isDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'))
            ->will($this->returnValue(false));

        $command->expects($this->at(3))
            ->method('makeDir')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'));

        $command->expects($this->at(4))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(5))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'bar.html'))
            ->will($this->returnValue(true));

        $command->expects($this->at(6))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'))
            ->will($this->returnValue(false));

        $command->expects($this->at(7))
            ->method('isFile')
            ->with($this->equalTo(PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'bar.html'))
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
        $command = Environment::getService('console')->find('generate:view');

        $this->assertInstanceOf('PSX\Framework\Console\Generate\ViewCommand', $command);
    }

    protected function assertSource($expect, $actual)
    {
        $expect = str_replace(array("\r\n", "\n", "\r"), "\n", $expect);
        $actual = str_replace(array("\r\n", "\n", "\r"), "\n", $actual);

        $this->assertEquals($expect, $actual);
    }

    protected function getExpectedControllerSource()
    {
        return <<<'PHP'
<?php

namespace Acme\Foo\Application;

use PSX\Framework\Controller\ViewAbstract;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class Bar extends ViewAbstract
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

    protected function getExpectedResourceSource()
    {
        return <<<'HTML'
<!DOCTYPE>
<html>
<head>
	<title>Controller</title>
</head>
<body>

<p><?php echo $message; ?></p>

</body>
</html>
HTML;
    }
}

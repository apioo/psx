<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * ViewCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ViewCommandTest extends CommandTestCase
{
	public function testCommand()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\ViewCommand')
			->setConstructorArgs(array(getContainer()))
			->setMethods(array('makeDir', 'writeFile'))
			->getMock();

		$command->expects($this->at(0))
			->method('makeDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application'));

		$command->expects($this->at(1))
			->method('makeDir')
			->with($this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource'));

		$command->expects($this->at(2))
			->method('writeFile')
			->with(
				$this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Bar.php'), 
				$this->callback(function($source){
					$this->assertSource($this->getExpectedControllerSource(), $source);
					return true;
				})
			);

		$command->expects($this->at(3))
			->method('writeFile')
			->with(
				$this->equalTo('library' . DIRECTORY_SEPARATOR . 'Acme' . DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'bar.html'), 
				$this->callback(function($source){
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

	public function testCommandAvailable()
	{
		$command = getContainer()->get('console')->find('generate:view');

		$this->assertInstanceOf('PSX\Console\Generate\ViewCommand', $command);
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

use PSX\Controller\ViewAbstract;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class Bar extends ViewAbstract
{
	/**
	 * @Inject
	 * @var Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @Inject
	 * @var PSX\TemplateInterface
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


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

use Symfony\Component\Console\Tester\CommandTester;

/**
 * ApiCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ApiCommandTest extends \PHPUnit_Framework_TestCase
{
	public function testCommand()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\ApiCommand')
			->setConstructorArgs(array(getContainer()))
			->setMethods(array('makeDir', 'writeFile'))
			->getMock();

		$command->expects($this->once())
			->method('makeDir')
			->with($this->equalTo('library\Acme\Foo'));

		$command->expects($this->once())
			->method('writeFile')
			->with(
				$this->equalTo('library\Acme\Foo\Bar.php'), 
				$this->callback(function($source){
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

	public function testCommandAvailable()
	{
		$command = getContainer()->get('console')->find('generate:api');

		$this->assertInstanceOf('PSX\Console\Generate\ApiCommand', $command);
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

use PSX\Controller\SchemaApiAbstract;
use PSX\Api\Documentation;
use PSX\Api\View;

/**
 * Bar
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class Bar extends SchemaApiAbstract
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

	/**
	 * @return PSX\Api\DocumentationInterface
	 */
	public function getDocumentation()
	{
		$view = new View();
		$view->setGet($this->schemaManager->get('Acme\Foo\Schema\GetResponse'));

		return new Documentation\Simple($view);
	}

	/**
	 * Returns the GET response
	 *
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doGet(Version $version)
	{
		return array(
			'message' => 'This is the default controller of PSX'
		);
	}

	/**
	 * Returns the POST response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doCreate(RecordInterface $record, Version $version)
	{
	}

	/**
	 * Returns the PUT response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doUpdate(RecordInterface $record, Version $version)
	{
	}

	/**
	 * Returns the DELETE response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doDelete(RecordInterface $record, Version $version)
	{
	}
}

PHP;
	}
}


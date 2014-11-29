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
 * BootstrapCacheCommandTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BootstrapCacheCommandTest extends CommandTestCase
{
	public function testCommand()
	{
		$command = $this->getMockBuilder('PSX\Console\Generate\BootstrapCacheCommand')
			->setMethods(array('makeDir', 'writeFile', 'getFiles'))
			->getMock();

		$command->expects($this->once())
			->method('writeFile')
			->with(
				$this->equalTo('cache/bootstrap.cache.php'), 
				$this->callback(function($source){
					$this->assertSource($this->getExpectedSource(), $source);
					return true;
				})
			);

		$command->expects($this->once())
			->method('getFiles')
			->will($this->returnValue(array(
				__DIR__ . '/BootstrapCache/Bar.php',
				__DIR__ . '/BootstrapCache/Foo.php',
			)));

		$commandTester = new CommandTester($command);
		$commandTester->execute(array(
		));
	}

	public function testCommandAvailable()
	{
		$command = getContainer()->get('console')->find('generate:bootstrap_cache');

		$this->assertInstanceOf('PSX\Console\Generate\BootstrapCacheCommand', $command);
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
namespace PSX\Console\Generate\BootstrapCache { class Bar { public function __construct() { } } } 
namespace PSX\Console\Generate\BootstrapCache { class Foo { protected function doBar($value) { } } } 

PHP;
	}
}


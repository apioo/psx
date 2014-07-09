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

namespace PSX\Command\ParameterParser;

use PSX\Command\ParameterParserTestCase;

/**
 * CliArgumentTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CliArgumentTest extends ParameterParserTestCase
{
	public function testParseArguments3()
	{
		$cliArgument = new CliArgument('Foo\Bar', array());

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array(), $cliArgument->getArgv());
	}

	public function testParseArguments4()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('foo'));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array(), $cliArgument->getArgv());
	}

	public function testParseArguments5()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('-foo', 'bar'));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array('foo' => 'bar'), $cliArgument->getArgv());
	}

	public function testParseArguments6()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('-foo', '-bar', 'bar'));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array('foo' => true, 'bar' => 'bar'), $cliArgument->getArgv());
	}

	public function testParseArguments7()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('-foo', '-bar', 'bar', 'bar', 'foo', '-test'));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array('foo' => true, 'bar' => 'bar', 'test' => null), $cliArgument->getArgv());
	}

	public function testParseArguments8()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('-', '-', '-'));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array(), $cliArgument->getArgv());
	}

	public function testParseArguments9()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('-foo'));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array('foo' => null), $cliArgument->getArgv());
	}

	public function testParseArguments10()
	{
		$cliArgument = new CliArgument('Foo\Bar', array('-foo', ''));

		$this->assertEquals('Foo\Bar', $cliArgument->getClassName());
		$this->assertEquals(array('foo' => ''), $cliArgument->getArgv());
	}

	protected function getParameterParser()
	{
		return new CliArgument('Foo\Bar', array('-foo', 'bar', '-bar', 'foo', '-flag'));
	}
}

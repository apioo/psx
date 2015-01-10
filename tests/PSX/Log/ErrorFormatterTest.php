<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Log;

/**
 * ErrorFormatterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ErrorFormatterTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->formatter = new ErrorFormatter();
	}

	protected function tearDown()
	{
		$this->formatter = null;
	}

	public function testFormatBasic()
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
		);

		$this->assertEquals('psx.INFO: foo', $this->formatter->format($record));
	}

	public function testFormatPhpError()
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
			'context'    => array(
				'severity' => E_WARNING,
			),
		);

		$this->assertEquals('psx.INFO: PHP Warning: foo', $this->formatter->format($record));
	}

	public function testFormatFileLine()
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
			'context'    => array(
				'file' => 'foo.php',
				'line' => 12,
			),
		);

		$this->assertEquals('psx.INFO: foo in foo.php on line 12', $this->formatter->format($record));
	}

	public function testFormatTrace()
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
			'context'    => array(
				'trace' => '#0 {main}',
			),
		);

		$this->assertEquals('psx.INFO: foo' . "\n" . 'Stack trace:' . "\n" . '#0 {main}', $this->formatter->format($record));
	}

	public function testFormatFull()
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
			'context'    => array(
				'file' => 'foo.php',
				'line' => 12,
				'trace' => '#0 {main}',
			),
		);

		$this->assertEquals('psx.INFO: foo in foo.php on line 12' . "\n" . 'Stack trace:' . "\n" . '#0 {main}', $this->formatter->format($record));
	}

	/**
	 * @dataProvider phpErrorProvider
	 */
	public function testAllPhpErrors($level, $name)
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
			'context'    => array(
				'severity' => $level,
			),
		);

		$this->assertEquals('psx.INFO: ' . $name . ': foo', $this->formatter->format($record));
	}

	public function phpErrorProvider()
	{
		return array(
			[E_ERROR, 'PHP Error'],
			[E_CORE_ERROR, 'PHP Core error'],
			[E_COMPILE_ERROR, 'PHP Compile error'],
			[E_USER_ERROR, 'PHP User error'],
			[E_RECOVERABLE_ERROR, 'PHP Recoverable error'],
			[E_WARNING, 'PHP Warning'],
			[E_CORE_WARNING, 'PHP Core warning'],
			[E_COMPILE_WARNING, 'PHP Compile warning'],
			[E_USER_WARNING, 'PHP User warning'],
			[E_PARSE, 'PHP Parse'],
			[E_NOTICE, 'PHP Notice'],
			[E_USER_NOTICE, 'PHP User notice'],
			[E_STRICT, 'PHP Strict'],
			[E_DEPRECATED, 'PHP Deprecated'],
			[E_USER_DEPRECATED, 'PHP User deprecated'],
		);
	}

	public function testUnknownPhpError()
	{
		$record = array(
			'channel'    => 'psx',
			'level_name' => 'INFO',
			'message'    => 'foo',
			'context'    => array(
				'severity' => 'foo',
			),
		);

		$this->assertEquals('psx.INFO: foo', $this->formatter->format($record));
	}
}

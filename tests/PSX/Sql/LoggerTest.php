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

namespace PSX\Sql;

use PSX\Config;
use PSX\Data\Record;
use PSX\DateTime;
use PSX\Sql\TableInterface;
use PSX\Test\TableDataSet;

/**
 * LoggerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
	public function testStartQuery()
	{
		$psrLogger = $this->getLogger();
		$psrLogger->expects($this->once())
			->method('debug')
			->with($this->equalTo('SELECT PI()'), $this->equalTo(array(
				'bar' => 'foo',
				'foo' => 'xxxxxxxxxxxxxxxxxxxxxxxxxx [...]',
				'bin' => '(binary value)',
			)));

		$logger = new Logger($psrLogger);
		$logger->startQuery('SELECT PI()', array('bar' => 'foo', 'foo' => str_repeat('x', 34), 'bin' => "\xFF\xFF"));
	}

	public function testStopQuery()
	{
		$logger = new Logger($this->getLogger());
		$logger->stopQuery();
	}

	protected function getLogger()
	{
		return $this->getMock('Psr\Log\LoggerInterface', array('emergency', 'alert', 'critical', 'warning', 'debug', 'log', 'info', 'notice', 'error'));
	}
}

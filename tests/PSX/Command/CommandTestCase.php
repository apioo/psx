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

namespace PSX\Command;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use PSX\Command\Executor;
use PSX\Command\ParameterParser\Map;
use PSX\Command\Output\Void;

/**
 * CommandTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CommandTestCase extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		parent::setUp();

		// we remove all used services so that our test has no side effects
		$serviceIds = getContainer()->getServiceIds();
		foreach($serviceIds as $serviceId)
		{
			getContainer()->set($serviceId, null);
		}

		// we replace the executor
		getContainer()->set('executor', new Executor(getContainer()->get('command_factory'), new Void()));

		// assign the phpunit test case
		getContainer()->set('test_case', $this);

		// set void logger
		$logger = new Logger('psx');
		$logger->pushHandler(new NullHandler());

		getContainer()->set('logger', $logger);
	}

	protected function tearDown()
	{
		parent::tearDown();

		// we remove all used services so that our test has no side effects
		$serviceIds = getContainer()->getServiceIds();
		foreach($serviceIds as $serviceId)
		{
			getContainer()->set($serviceId, null);
		}
	}

	/**
	 * Loads an specific command
	 *
	 * @param array parameters
	 * @return PSX\CommandAbstract
	 */
	protected function loadCommand($className, array $parameters)
	{
		return getContainer()->get('executor')->run(new Map($className, $parameters));
	}
}

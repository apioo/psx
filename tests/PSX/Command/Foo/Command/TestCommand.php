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

namespace PSX\Command\Foo\Command;

use PSX\CommandAbstract;
use PSX\Command\Parameters;
use PSX\Command\OutputInterface;

/**
 * TestCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestCommand extends CommandAbstract
{
	public function onExecute(Parameters $parameters, OutputInterface $output)
	{
		// inspect inner module API
		$testCase = $this->getTestCase();

		// get container
		$testCase->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->getContainer());

		// get location
		$testCase->assertInstanceOf('PSX\Loader\Location', $this->getLocation());

		// get config
		$testCase->assertInstanceOf('PSX\Config', $this->getConfig());

		// test properties
		$testCase->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->container);
		$testCase->assertInstanceOf('PSX\Loader\Location', $this->location);
		$testCase->assertInstanceOf('PSX\Config', $this->config);
	}
}

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

namespace PSX\Console;

use PSX\Command\Executor;
use PSX\Command\ParameterParser;
use PSX\Loader\RoutingParserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RouteCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RouteCommand extends Command
{
	protected $routingParser;

	public function __construct(RoutingParserInterface $routingParser)
	{
		parent::__construct();

		$this->routingParser = $routingParser;
	}

	protected function configure()
	{
		$this
			->setName('route')
			->setDescription('Displays all available routes');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$collection = $this->routingParser->getCollection();
		$rows       = array();

		foreach($collection as $route)
		{
			$rows[] = array(implode('|', $route[0]), $route[1], $route[2]);
		}

		$table = $this->getHelper('table');
		$table
			->setLayout(TableHelper::LAYOUT_COMPACT)
			->setRows($rows);

		$table->render($output);
	}
}

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

namespace PSX\Console;

use PSX\Command\Executor;
use PSX\Command\ParameterParser;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CommandCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CommandCommand extends Command
{
	protected $executor;
	protected $reader;

	public function __construct(Executor $executor, ReaderInterface $reader)
	{
		parent::__construct();

		$this->executor = $executor;
		$this->reader   = $reader;
	}

	public function setReader(ReaderInterface $reader)
	{
		$this->reader = $reader;
	}

	protected function configure()
	{
		$this
			->setName('command')
			->setDescription('Executes an PSX command through the console. The parameters must be either provided as JSON via stdin or per parameter')
			->addArgument('cmd', InputArgument::REQUIRED, 'Name of the command')
			->addArgument('parameters', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Needed parameters for the command')
			->addOption('stdin', 's', InputOption::VALUE_NONE, 'Whether to read from stdin');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$command    = $input->getArgument('cmd');
		$parameters = $input->getArgument('parameters');

		if($input->getOption('stdin'))
		{
			$body = $this->reader->read();
			if(empty($body))
			{
				$body = '{}';
			}

			$parser = new ParameterParser\Json($command, $body);
		}
		else
		{
			$map = array();
			foreach($parameters as $parameter)
			{
				$parts = explode(':', $parameter, 2);
				$key   = $parts[0];
				$value = isset($parts[1]) ? $parts[1] : null;

				$map[$key] = $value;
			}

			$parser = new ParameterParser\Map($command, $map);
		}

		$this->executor->run($parser);
	}
}

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
			->setDescription('Executes an PSX command through the console. The parameters must be provided as JSON via stdin')
			->addArgument('cmd', InputArgument::REQUIRED, 'Name of the command');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$body = $this->reader->read();

		if(empty($body))
		{
			$body = '{}';
		}

		$this->executor->run(new ParameterParser\Json($input->getArgument('cmd'), $body));
	}
}

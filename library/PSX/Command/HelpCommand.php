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

use PSX\CommandAbstract;
use PSX\Command\Parameter;
use PSX\Command\Parameters;
use PSX\Command\OutputInterface;
use PSX\Loader\Location;

/**
 * HelpCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HelpCommand extends CommandAbstract
{
	/**
	 * @Inject
	 * @var PSX\Command\Executor
	 */
	protected $executor;

	/**
	 * @Inject
	 * @var PSX\Dispatch\CommandFactoryInterface
	 */
	protected $commandFactory;

	public function onExecute(Parameters $parameters, OutputInterface $output)
	{
		$className = $parameters->get('c');

		if(!empty($className))
		{
			$this->printHelp($className, $output);
		}
		else
		{
			$this->printHelp(__CLASS__, $output);
		}
	}

	public function getParameters()
	{
		return $this->getParameterBuilder()
			->setDescription('Displays informations about an command')
			->addOption('c', Parameter::TYPE_OPTIONAL, 'The name of the class or the alias')
			->getParameters();
	}

	protected function printHelp($className, OutputInterface $output)
	{
		$className  = $this->executor->getClassName($className);
		$command    = $this->commandFactory->getCommand($className, new Location());
		$parameters = $command->getParameters();

		$maxLength  = 0;
		$values     = array();

		foreach($parameters as $parameter)
		{
			$command     = '-' . $parameter->getName() . ($parameter->getType() == Parameter::TYPE_FLAG ? '' : ' <value>');
			$description = $parameter->getDescription();
			$length      = strlen($command);

			if($length > $maxLength)
			{
				$maxLength = $length;
			}

			$values[] = array($command, $description);
		}

		$output->writeln('Usage:');
		$output->writeln('  psx ' . $className . ' [options]');
		$output->writeln('');

		// print description
		$description = $parameters->getDescription();

		if(!empty($description))
		{
			$output->writeln('Description:');
			$output->writeln('  ' . wordwrap($description, 75, "\n" . '  '));
			$output->writeln('');
		}

		// print commands
		$output->writeln('Options:');

		if(count($values) > 0)
		{
			foreach($values as $value)
			{
				$output->writeln('  ' . str_pad($value[0], $maxLength) . '  ' . wordwrap($value[1], 75, "\n" . str_repeat(' ', $maxLength + 4)));
			}
		}
		else
		{
			$output->writeln('  ' . 'No parameters available');
		}

		$output->writeln('');
	}
}

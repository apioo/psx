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

namespace PSX\Controller\Tool;

use PSX\Command\ParameterParser;
use PSX\Controller\ViewAbstract;
use PSX\Loader\Location;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * CommandController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CommandController extends ViewAbstract
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

	/**
	 * @Inject
	 * @var Monolog\Logger
	 */
	protected $logger;

	public function onGet()
	{
		parent::onGet();

		$this->template->set(__DIR__ . '/../Resource/command_controller.tpl');

		$commandClass = $this->getParameter('command');

		if(!empty($commandClass))
		{
			$command    = $this->commandFactory->getCommand($commandClass, new Location());
			$parameters = $command->getParameters();
			$data       = array();

			foreach($parameters as $parameter)
			{
				$data[] = array(
					'name'        => $parameter->getName(),
					'description' => $parameter->getDescription(),
					'type'        => $parameter->getType(),
				);
			}

			$this->setBody(array(
				'command'     => $commandClass,
				'description' => $parameters->getDescription(),
				'parameters'  => $data,
			));
		}
		else
		{
			$this->setBody(array(
				'commands' => $this->executor->getAliases(),
			));
		}
	}

	public function onPost()
	{
		parent::onPost();

		$commandClass = $this->getParameter('command');
		$parameters   = $this->getBody();
		$parameters   = !empty($parameters) ? $parameters : array();

		if(!empty($commandClass))
		{
			$stream = fopen('php://memory', 'r+');

			$this->logger->pushHandler(new StreamHandler($stream, Logger::DEBUG));

			$this->executor->run(new ParameterParser\Map($commandClass, $parameters));

			$output = stream_get_contents($stream, -1, 0);

			$this->logger->popHandler();

			$this->setBody(array(
				'output' => $output,
			));
		}
		else
		{
			throw new \Exception('Command not available');
		}
	}
}

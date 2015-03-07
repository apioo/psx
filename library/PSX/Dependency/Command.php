<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Dependency;

use PSX\Command\Executor;
use PSX\Command\Output\Logger;
use PSX\Command\Output\Stdout;
use PSX\Command\StdinReader;
use PSX\Dispatch\CommandFactory;

/**
 * Command
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait Command
{
	/**
	 * @return PSX\Dispatch\CommandFactoryInterface
	 */
	public function getCommandFactory()
	{
		return new CommandFactory($this->get('object_builder'));
	}

	/**
	 * @return PSX\Command\OutputInterface
	 */
	public function getCommandOutput()
	{
		return PHP_SAPI == 'cli' ? new Stdout() : new Logger($this->get('logger'));
	}

	/**
	 * @return PSX\Command\Executor
	 */
	public function getExecutor()
	{
		return new Executor($this->get('command_factory'), $this->get('command_output'), $this->get('event_dispatcher'));
	}
}

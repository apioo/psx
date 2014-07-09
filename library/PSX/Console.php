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

namespace PSX;

use PSX\Command\Executor;
use PSX\Command\ParameterParser;
use PSX\Config;

/**
 * Console
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Console
{
	protected $executor;
	protected $config;

	public function __construct(Executor $executor, Config $config)
	{
		$this->executor = $executor;
		$this->config   = $config;
	}

	public function run()
	{
		try
		{
			$fileName  = array_shift($_SERVER['argv']);
			$className = array_shift($_SERVER['argv']);

			if(in_array('--stdin', $_SERVER['argv']))
			{
				$body = '';
				while(!feof(STDIN))
				{
					$line = fgets(STDIN);

					if($line[0] == chr(4))
					{
						break;
					}

					$body.= $line;
				}

				$this->executor->run(new ParameterParser\Json($className, $body));
			}
			else
			{
				if(empty($className))
				{
					$className = 'PSX\Command\ListCommand';
				}

				$this->executor->run(new ParameterParser\CliArgument($className, $_SERVER['argv']));
			}
		}
		catch(\Exception $e)
		{
			echo $e->getMessage() . PHP_EOL . PHP_EOL;

			if($this->config['psx_debug'] === true)
			{
				echo $e->getTraceAsString() . PHP_EOL;
			}
		}
	}
}

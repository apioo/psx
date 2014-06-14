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

namespace PSX\Command\ParameterParser;

use InvalidArgumentException;
use PSX\Command\ParameterParserInterface;
use PSX\Command\Parameters;

/**
 * CliArgument
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CliArgument extends Map
{
	public function __construct(array $argv)
	{
		$fileName  = array_shift($argv);
		$className = array_shift($argv);

		if(empty($className))
		{
			$className = 'PSX\Command\ListCommand';
		}

		parent::__construct($className, $this->getArray($argv));
	}

	protected function getArray(array $argv)
	{
		$result = array();
		$len    = count($argv);

		for($i = 0; $i < $len; $i++)
		{
			$name = $argv[$i];

			if(isset($name[0]) && $name[0] == '-')
			{
				$key = substr($name, 1);

				if(!empty($key))
				{
					if(isset($argv[$i + 1]) && isset($argv[$i + 1][0]) && $argv[$i + 1][0] == '-')
					{
						$value = true;
					}
					else
					{
						$i++;
						$value = isset($argv[$i]) ? $argv[$i] : null;
					}

					$result[$key] = $value;
				}
			}
		}

		return $result;
	}
}

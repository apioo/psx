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

namespace PSX\Command\ParameterParser;

use PSX\Command\MissingParameterException;
use PSX\Command\Parameter;
use PSX\Command\ParameterParserInterface;
use PSX\Command\Parameters;

/**
 * Map
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Map implements ParameterParserInterface
{
	protected $className;
	protected $argv;

	public function __construct($className, array $argv = array())
	{
		$this->className = $className;
		$this->argv      = $argv;
	}

	public function getClassName()
	{
		return $this->className;
	}

	public function getArgv()
	{
		return $this->argv;
	}

	public function fillParameters(Parameters $parameters)
	{
		foreach($parameters as $parameter)
		{
			if(array_key_exists($parameter->getName(), $this->argv))
			{
				if($parameter->getType() == Parameter::TYPE_FLAG)
				{
					$parameter->setValue(true);
				}
				else
				{
					$parameter->setValue($this->argv[$parameter->getName()]);
				}
			}

			if($parameter->getType() == Parameter::TYPE_REQUIRED && !$parameter->hasValue())
			{
				throw new MissingParameterException('Parameter "' . $parameter->getName() . '" is missing');
			}

			if($parameter->getType() == Parameter::TYPE_FLAG && !$parameter->hasValue())
			{
				$parameter->setValue(false);
			}
		}
	}
}

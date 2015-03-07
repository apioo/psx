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

namespace PSX\Command\ParameterParser;

use PSX\Command\MissingParameterException;
use PSX\Command\Parameter;
use PSX\Command\ParameterParserInterface;
use PSX\Command\Parameters;

/**
 * Map
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

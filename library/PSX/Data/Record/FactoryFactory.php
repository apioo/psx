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

namespace PSX\Data\Record;

use InvalidArgumentException;

/**
 * This factory produces record factory classes. If you have an annotation in an 
 * record which points to an class which implements the FactoryInterface the
 * factory method will be called to create the factory class. If your factory 
 * has dependecies i.e. an database connection you can build your own factory
 * to provide such dependencies
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FactoryFactory
{
	public function getFactory($className)
	{
		if(class_exists($className))
		{
			$factory = $this->createInstance($className);

			if($factory instanceof FactoryInterface)
			{
				return $factory;
			}
			else
			{
				throw new InvalidArgumentException('Factory must be an instanceof PSX\Data\Record\FactoryInterface');
			}
		}
		else
		{
			throw new InvalidArgumentException('Factory class "' . $className . '" does not exist');
		}
	}

	protected function createInstance($className)
	{
		return new $className();
	}
}

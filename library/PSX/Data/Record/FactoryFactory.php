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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

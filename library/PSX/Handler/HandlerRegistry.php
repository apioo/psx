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

namespace PSX\Handler;

use BadMethodCallException;
use RuntimeException;
use PSX\Sql\Condition;

/**
 * HandlerQueryInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HandlerRegistry
{
	protected $manager = array();

	public function add(HandlerManagerInterface $manager)
	{
		$this->manager[] = $manager;
	}

	public function get($name)
	{
		foreach($this->manager as $manager)
		{
			if($manager->getName() == $name)
			{
				return $manager;
			}
		}

		return null;
	}

	public function __call($method, array $arguments)
	{
		if(substr($method, 0, 3) == 'get')
		{
			$name    = lcfirst(substr($method, 3));
			$manager = $this->get($name);

			if($manager instanceof HandlerManagerInterface)
			{
				$className = isset($arguments[0]) ? $arguments[0] : null;

				if(!empty($className))
				{
					return $manager->get($className);
				}
				else
				{
					throw new RuntimeException('First argument must be an class name');
				}
			}
			else
			{
				throw new RuntimeException('Found no manager for "' . $name . '"');
			}
		}
		else
		{
			throw new BadMethodCallException('Undefined method ' . $method);
		}
	}
}

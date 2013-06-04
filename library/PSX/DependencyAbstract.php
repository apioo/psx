<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Config;

/**
 * DependencyAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DependencyAbstract implements DependencyInterface
{
	private static $_container = array();

	public function __construct(Config $config)
	{
		$methods = get_class_methods($this);

		foreach($methods as $method)
		{
			$service = lcfirst(substr($method, 3));

			if(substr($method, 0, 3) == 'get' && !empty($service))
			{
				self::$_container[$service] = null;
			}
		}

		self::set('config', $config);
	}

	public function set($name, $obj)
	{
		return self::$_container[$name] = $obj;
	}

	public function get($name)
	{
		if(!isset(self::$_container[$name]))
		{
			$method = 'get' . ucfirst($name);

			if(self::has($name))
			{
				self::$_container[$name] = $this->$method();
			}
			else
			{
				throw new Exception('Service not defined');
			}
		}

		return self::$_container[$name];
	}

	public function has($name)
	{
		return array_key_exists($name, self::$_container);
	}
}

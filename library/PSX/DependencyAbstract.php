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

/**
 * DependencyAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DependencyAbstract implements DependencyInterface
{
	protected $services   = array();
	protected $parameters = array();

	public function __construct()
	{
		$methods = get_class_methods($this);

		foreach($methods as $method)
		{
			$service = lcfirst(substr($method, 3));

			if(substr($method, 0, 3) == 'get' && !empty($service) && $service != 'parameter')
			{
				$this->services[$service] = null;
			}
		}
	}

	public function set($name, $obj)
	{
		return $this->services[$name] = $obj;
	}

	public function get($name)
	{
		if(!isset($this->services[$name]))
		{
			$method = 'get' . ucfirst($name);

			if($this->has($name))
			{
				$this->services[$name] = $this->$method();
			}
			else
			{
				throw new Exception('Service "' . $name . '" not defined');
			}
		}

		return $this->services[$name];
	}

	public function has($name)
	{
		return array_key_exists($name, $this->services);
	}

	public function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	public function getParameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}
}

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
abstract class DependencyAbstract
{
	private static $_container = array();

	protected $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function set($name, $obj)
	{
		return self::$_container[$name] = $obj;
	}

	public function has($name)
	{
		return isset(self::$_container[$name]);
	}

	public function get($name)
	{
		return self::$_container[$name];
	}

	/**
	 * This array are the properties wich are available in an module if it uses
	 * this dependency
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return self::$_container;
	}

	/**
	 * Method wich initializes all services wich sould be directly available in 
	 * an module
	 *
	 * @return void
	 */
	public function setup()
	{
		$this->getLoader();
	}

	/**
	 * Search the container for an object wich is an instanceof $type. Returns
	 * the object or null if nothing is found
	 *
	 * @return object
	 */
	public function getByType($type)
	{
		foreach(self::$_container as $obj)
		{
			if($obj instanceof $type)
			{
				return $obj;
			}
		}

		return null;
	}

	public function getBase()
	{
		if($this->has('base'))
		{
			return $this->get('base');
		}

		return $this->set('base', new Base($this->getConfig()));
	}

	public function getConfig()
	{
		if($this->has('config'))
		{
			return $this->get('config');
		}

		return $this->set('config', $this->config);
	}

	public function getLoader()
	{
		if($this->has('loader'))
		{
			return $this->get('loader');
		}

		return $this->set('loader', new Loader($this->getBase()));
	}
}

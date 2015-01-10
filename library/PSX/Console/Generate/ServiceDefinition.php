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

namespace PSX\Console\Generate;

/**
 * ServiceDefinition
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ServiceDefinition
{
	protected $namespace;
	protected $className;
	protected $services;
	protected $isDryRun;

	public function __construct($namespace, $className, $isDryRun)
	{
		$this->namespace = $namespace;
		$this->className = $className;
		$this->isDryRun  = $isDryRun;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

	public function getClassName()
	{
		return $this->className;
	}

	public function setClassName($className)
	{
		$this->className = $className;
	}

	public function getServices()
	{
		return $this->services;
	}

	public function setServices(array $services)
	{
		$this->services = $services;
	}

	public function isDryRun()
	{
		return $this->isDryRun;
	}

	public function getPath()
	{
		return PSX_PATH_LIBRARY . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $this->namespace);
	}
}

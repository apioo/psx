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

namespace PSX\Dependency;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ScopeInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * A simple and fast implementation of an dependency container. Note this 
 * implementation does not support nested scopes. You can enter a scope and when 
 * you leave the scope you are at the root scope
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Container implements ContainerInterface
{
	protected $services   = array();
	protected $parameters = array();
	protected $scope      = 'container';
	protected $scopes     = array('container');

	public function __construct()
	{
		$this->services[$this->scope]   = array();
		$this->parameters[$this->scope] = array();
	}

	public function set($name, $obj, $scope = 'container')
	{
		if(!in_array($scope, $this->scopes))
		{
			throw new InvalidArgumentException('Invalid scope');
		}

		$name = self::normalizeName($name);

		return $this->services[$scope][$name] = $obj;
	}

	public function get($name, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
	{
		$name = self::normalizeName($name);

		if(!isset($this->services[$this->scope][$name]))
		{
			if(method_exists($this, $method = 'get' . $name))
			{
				$this->services[$this->scope][$name] = $this->$method();
			}
			else
			{
				if($invalidBehavior == self::EXCEPTION_ON_INVALID_REFERENCE)
				{
					throw new ServiceNotFoundException('Service ' . $name . ' not defined');
				}
				else if($invalidBehavior == self::NULL_ON_INVALID_REFERENCE)
				{
					return null;
				}
			}
		}

		return $this->services[$this->scope][$name];
	}

	public function has($name)
	{
		$name = self::normalizeName($name);

		return isset($this->services[$this->scope][$name]) || method_exists($this, 'get' . $name);
	}

	public function setParameter($name, $value)
	{
		$name = strtolower($name);

		$this->parameters[$this->scope][$name] = $value;
	}

	public function getParameter($name)
	{
		$name = strtolower($name);

		if($this->hasParameter($name))
		{
			return $this->parameters[$this->scope][$name];
		}
		else
		{
			throw new InvalidArgumentException('Parameter not set');
		}
	}

	public function hasParameter($name)
	{
		$name = strtolower($name);

		return isset($this->parameters[$this->scope][$name]);
	}

	public function enterScope($name)
	{
		if(!$this->hasScope($name))
		{
			throw new InvalidArgumentException('Scope does not exist');
		}

		$this->scope = $name;
	}

	public function leaveScope($name)
	{
		$this->scope = 'container';
	}

	public function addScope(ScopeInterface $scope)
	{
		$this->scopes[] = $scope->getName();

		$this->services[$scope->getName()]   = array();
		$this->parameters[$scope->getName()] = array();
	}

	public function hasScope($name)
	{
		return in_array($name, $this->scopes);
	}

	public function isScopeActive($name)
	{
		return $this->scope == $name;
	}

	public static function normalizeName($name)
	{
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
	}
}

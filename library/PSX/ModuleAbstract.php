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

use PSX\Dependency;
use PSX\Loader\Location;

/**
 * ModuleAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ModuleAbstract
{
	protected $location;
	protected $base;
	protected $config;
	protected $basePath;
	protected $uriFragments = array();

	protected $_container;

	public function __construct(Location $location, Base $base, $basePath, array $uriFragments)
	{
		$this->location     = $location;
		$this->base         = $base;
		$this->config       = $base->getConfig();
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;
	}

	/**
	 * Returns the dependency container for the module
	 *
	 * @return PSX\DependencyAbstract
	 */
	public function getDependencies()
	{
		return new Dependency\Request($this->getConfig());
	}

	public function _ini()
	{
		// load dependencies
		$this->_container = $this->getDependencies();

		// call event methods
		$this->onLoad();

		switch($this->getMethod())
		{
			case 'GET':
				$this->onGet();
				break;

			case 'POST':
				$this->onPost();
				break;

			case 'PUT':
				$this->onPut();
				break;

			case 'DELETE':
				$this->onDelete();
				break;
		}
	}

	/**
	 * If the called method starts with "get" the matching service from the di 
	 * container is returned else null
	 *
	 * @return object
	 */
	public function __call($name, $args)
	{
		if(substr($name, 0, 3) == 'get')
		{
			$service = lcfirst(substr($name, 3));

			if($this->_container->has($service))
			{
				return $this->_container->get($service);
			}

			return null;
		}
	}

	public function onLoad()
	{
	}

	public function onGet()
	{
	}

	public function onPost()
	{
	}

	public function onPut()
	{
	}

	public function onDelete()
	{
	}

	/**
	 * Is called so that the module can process the content that means i.e. load
	 * a template or output the content. The $content contains all output wich 
	 * was captured through output buffering
	 *
	 * @param string $content
	 * @return string
	 */
	public function processResponse($content)
	{
		return $content;
	}

	protected function getConfig()
	{
		return $this->base->getConfig();
	}

	protected function getLocation()
	{
		return $this->location;
	}

	protected function getBasePath()
	{
		return $this->basePath;
	}

	protected function getUriFragments($key = null)
	{
		if($key !== null)
		{
			return isset($this->uriFragments[$key]) ? $this->uriFragments[$key] : null;
		}
		else
		{
			return $this->uriFragments;
		}
	}

	protected function getValidator()
	{
		return $this->validate;
	}

	protected function getMethod()
	{
		return Base::getRequestMethod();
	}

	protected function getUrl()
	{
		return $this->base->getRequest()->getUrl();
	}

	protected function getHeader($key = null)
	{
		return Base::getRequestHeader($key);
	}

	protected function getParameter()
	{
		return $this->parameter;
	}

	protected function getBody()
	{
		return $this->body;
	}

	protected function getContainer()
	{
		return $this->_container;
	}

	protected function setResponseCode($code)
	{
		Base::setResponseCode($code);
	}
}


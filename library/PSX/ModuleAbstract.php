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
	protected $container;
	protected $location;
	protected $basePath;
	protected $uriFragments = array();

	protected $base;
	protected $config;

	public function __construct($container, Location $location, $basePath, array $uriFragments)
	{
		$this->container    = $container;
		$this->location     = $location;
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;

		$this->base         = $container->get('base');
		$this->config       = $container->get('config');
	}

	/**
	 * Returns an array of request filters wich are applied on the current 
	 * request
	 *
	 * @return array<PSX\Dispatch\RequestFilterInterface>
	 */
	public function getRequestFilter()
	{
		return array();
	}

	/**
	 * Returns an array of response filters wich are applied on the response
	 *
	 * @return array<PSX\Dispatch\ResponseFilterInterface>
	 */
	public function getResponseFilter()
	{
		return array();
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

			if($this->container->has($service))
			{
				return $this->container->get($service);
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

	protected function getContainer()
	{
		return $this->container;
	}

	protected function getLocation()
	{
		return $this->location;
	}

	protected function getBase()
	{
		return $this->base;
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

	protected function getConfig()
	{
		return $this->config;
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
		return $this->container->get('inputGet');
	}

	protected function getBody()
	{
		return $this->container->get('inputPost');
	}

	protected function setResponseCode($code)
	{
		Base::setResponseCode($code);
	}
}


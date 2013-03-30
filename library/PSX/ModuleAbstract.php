<?php
/*
 *  $Id: ModuleAbstract.php 653 2012-10-06 22:20:05Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_ModuleAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Module
 * @version    $Revision: 653 $
 */
abstract class ModuleAbstract
{
	protected $location;
	protected $base;
	protected $basePath;
	protected $uriFragments = array();

	protected $_container;

	public function __construct(Location $location, Base $base, $basePath, array $uriFragments)
	{
		$this->location     = $location;
		$this->base         = $base;
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;
	}

	/**
	 * Returns the dependency container for the module
	 *
	 * @return PSX_DependencyAbstract
	 */
	public function getDependencies()
	{
		return new Dependency\Request($this->base->getConfig());
	}

	public function _ini()
	{
		// load dependencies
		$this->_container = $this->getDependencies();
		$this->_container->setup();

		$parameters = $this->_container->getParameters();

		foreach($parameters as $k => $obj)
		{
			$this->$k = $obj;
		}

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


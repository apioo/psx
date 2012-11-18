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
abstract class PSX_ModuleAbstract
{
	protected $location;
	protected $base;
	protected $basePath;
	protected $uriFragments = array();

	protected $_validate;
	protected $_parameter;
	protected $_body;

	public function __construct(PSX_Loader_Location $location, PSX_Base $base, $basePath, array $uriFragments)
	{
		$this->location     = $location;
		$this->base         = $base;
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;

		// default objects
		$this->_validate  = new PSX_Validate();
		$this->_parameter = new PSX_Input_Get($this->_validate);
		$this->_body      = new PSX_Input_Post($this->_validate);

		// assign dependencies
		$dependencies = $this->getDependencies();

		if($dependencies instanceof PSX_DependencyAbstract)
		{
			$parameters = $dependencies->getParameters();

			foreach($parameters as $k => $obj)
			{
				$this->$k = $obj;
			}
		}
	}

	public function getDependencies()
	{
		return new PSX_Dependency_Default();
	}

	public function _ini()
	{
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
		return $this->_validate;
	}

	protected function getMethod()
	{
		return PSX_Base::getRequestMethod();
	}

	protected function getUrl()
	{
		return $this->base->getRequest()->getUrl();
	}

	protected function getHeaders()
	{
		return PSX_Base::getRequestHeader();
	}

	protected function getHeader($key)
	{
		return PSX_Base::getRequestHeader($key);
	}

	protected function getParameter()
	{
		return $this->_parameter;
	}

	protected function getBody()
	{
		return $this->_body;
	}
}


<?php
/*
 *  $Id: DependencyAbstract.php 530 2012-06-26 20:51:06Z k42b3.x@googlemail.com $
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
 * PSX_DependencyAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Dependency
 * @version    $Revision: 530 $
 */
abstract class PSX_DependencyAbstract
{
	private static $_container = array();

	protected $config;

	public function __construct(PSX_Config $config)
	{
		$this->config = $config;

		$this->setup();
	}

	public function setService($name, $obj)
	{
		return self::$_container[$name] = $obj;
	}

	public function hasService($name)
	{
		return isset(self::$_container[$name]);
	}

	public function getService($name)
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
	protected function setup()
	{
		$this->getConfig();
		$this->getLoader();
	}

	public function getBase()
	{
		if($this->hasService('base'))
		{
			return $this->getService('base');
		}

		return $this->setService('base', PSX_Base::getInstance());
	}

	public function getConfig()
	{
		if($this->hasService('config'))
		{
			return $this->getService('config');
		}

		return $this->setService('config', $this->getBase()->getConfig());
	}

	public function getLoader()
	{
		if($this->hasService('loader'))
		{
			return $this->getService('loader');
		}

		return $this->setService('loader', $this->getBase()->getLoader());
	}
}


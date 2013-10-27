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

namespace PSX\Loader;

use PSX\ModuleAbstract;

/**
 * ProbeModule
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ProbeModule extends ModuleAbstract
{
	protected $methodsCalled = array();

	public function __construct($container, Location $location, $basePath, array $uriFragments)
	{
		parent::__construct($container, $location, $basePath, $uriFragments);

		$this->methodsCalled[] = __METHOD__;
	}

	public function getStage()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::getStage();
	}

	public function getRequestFilter()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::getRequestFilter();
	}

	public function getResponseFilter()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::getResponseFilter();
	}

	public function onLoad()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function onGet()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function onPost()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function onPut()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function onDelete()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function processResponse($content)
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::processResponse($content);
	}

	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doIndex()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod GET
	 * @path /detail/{id}
	 */
	public function doShowDetails()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod POST
	 * @path /
	 */
	public function doInsert()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod POST
	 * @path /foo
	 */
	public function doInsertNested()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod PUT
	 * @path /
	 */
	public function doUpdate()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod PUT
	 * @path /foo
	 */
	public function doUpdateNested()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod DELETE
	 * @path /
	 */
	public function doDelete()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	/**
	 * @httpMethod DELETE
	 * @path /foo
	 */
	public function doDeleteNested()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function getMethodsCalled()
	{
		return $this->methodsCalled;
	}

	public function getFragments()
	{
		return $this->uriFragments;
	}
}

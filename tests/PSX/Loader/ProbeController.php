<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\ControllerAbstract;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Loader\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ProbeController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ProbeController extends ControllerAbstract
{
	protected $methodsCalled = array();

	public function __construct(ContainerInterface $container, Location $location, Request $request, Response $response)
	{
		parent::__construct($container, $location, $request, $response);

		$this->methodsCalled[] = __METHOD__;
	}

	public function getStage()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::getStage();
	}

	public function getPreFilter()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::getPreFilter();
	}

	public function getPostFilter()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::getPostFilter();
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

	public function processResponse()
	{
		$this->methodsCalled[] = __METHOD__;

		return parent::processResponse();
	}

	public function doIndex()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function doShowDetails()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function doInsert()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function doInsertNested()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function doUpdate()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function doUpdateNested()
	{
		$this->methodsCalled[] = __METHOD__;
	}

	public function doDelete()
	{
		$this->methodsCalled[] = __METHOD__;
	}

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

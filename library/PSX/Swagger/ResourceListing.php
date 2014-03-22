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

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * ResourceListing
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResourceListing extends RecordAbstract
{
	protected $swaggerVersion;
	protected $apiVersion;
	protected $info;
	protected $authorizations;
	protected $apis = array();

	public function __construct($apiVersion = null)
	{
		$this->swaggerVersion = Swagger::VERSION;
		$this->apiVersion     = $apiVersion;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion = $apiVersion;
	}
	
	public function getApiVersion()
	{
		return $this->apiVersion;
	}

	/**
	 * @param PSX\Swagger\InfoObject $info
	 */
	public function setInfo(InfoObject $info)
	{
		$this->info = $info;
	}

	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * @param array<PSX\Swagger\ResourceObject> $apis
	 */
	public function setApis(array $apis)
	{
		$this->apis = $apis;
	}
	
	public function getApis()
	{
		return $this->apis;
	}

	public function addResource(ResourceObject $resourceObject)
	{
		$this->apis[] = $resourceObject;
	}
}

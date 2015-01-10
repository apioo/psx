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

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * Declaration
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Declaration extends RecordAbstract
{
	protected $swaggerVersion;
	protected $apiVersion;
	protected $basePath;
	protected $resourcePath;
	protected $apis = array();
	protected $models = array();

	public function __construct($apiVersion = null, $basePath = null, $resourcePath = null)
	{
		$this->swaggerVersion = Swagger::VERSION;
		$this->apiVersion     = $apiVersion;
		$this->basePath       = $basePath;

		if($resourcePath !== null)
		{
			$this->setResourcePath($resourcePath);
		}
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion = $apiVersion;
	}
	
	public function getApiVersion()
	{
		return $this->apiVersion;
	}

	public function setBasePath($basePath)
	{
		$this->basePath = $basePath;
	}
	
	public function getBasePath()
	{
		return $this->basePath;
	}

	public function setResourcePath($resourcePath)
	{
		$this->resourcePath = '/' . ltrim($resourcePath, '/');
	}

	public function getResourcePath()
	{
		return $this->resourcePath;
	}

	/**
	 * @param array<PSX\Swagger\Api> $api
	 */
	public function setApis(array $api)
	{
		$this->apis = $api;
	}

	public function getApis()
	{
		return $this->apis;
	}

	public function addApi(Api $api)
	{
		$this->apis[] = $api;
	}

	/**
	 * @param PSX\Swagger\ModelFactory $models
	 */
	public function setModels(array $models)
	{
		$this->models = $models;
	}

	public function getModels()
	{
		return $this->models;
	}

	public function addModel(Model $model)
	{
		$this->models[$model->getId()] = $model;
	}
}

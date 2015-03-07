<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * Declaration
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

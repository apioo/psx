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

namespace PSX\Api\Resource\Generator;

use PSX\Api\Resource;
use PSX\Api\Resource\GeneratorAbstract;
use PSX\Data\SchemaInterface;
use PSX\Data\Writer\Json as JsonWriter;
use PSX\Json;
use PSX\Swagger\Api;
use PSX\Swagger\Declaration;
use PSX\Swagger\Model;
use PSX\Swagger\Operation;
use PSX\Swagger\Parameter;
use PSX\Swagger\ResponseMessage;
use PSX\Util\ApiGeneration;

/**
 * Generates an Swagger 1.2 representation of an API resource. Note this does 
 * not generate a resource listing only the documentation of an single resource
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Swagger extends GeneratorAbstract
{
	protected $apiVersion;
	protected $basePath;
	protected $targetNamespace;

	public function __construct($apiVersion, $basePath, $targetNamespace)
	{
		$this->apiVersion      = $apiVersion;
		$this->basePath        = $basePath;
		$this->targetNamespace = $targetNamespace;
	}

	public function generate(Resource $resource)
	{
		$declaration = new Declaration($this->apiVersion);
		$declaration->setBasePath($this->basePath);
		$declaration->setApis($this->getApis($resource));
		$declaration->setModels($this->getModels($resource));
		$declaration->setResourcePath(ApiGeneration::transformRoutePlaceholder($resource->getPath()));

		$writer  = new JsonWriter();
		$swagger = $writer->write($declaration);

		// since swagger does not fully support the json schema spec we must 
		// remove the $ref fragments
		$swagger = str_replace('#\/definitions\/', '', $swagger);

		return $swagger;
	}

	protected function getApis(Resource $resource)
	{
		$api     = new Api(ApiGeneration::transformRoutePlaceholder($resource->getPath()));
		$methods = $resource->getMethods();

		foreach($methods as $method)
		{
			// get operation name
			$request    = $method->getRequest();
			$response   = $this->getSuccessfulResponse($method);
			$entityName = '';

			if($request instanceof SchemaInterface)
			{
				$entityName = $request->getDefinition()->getName();
			}
			else if($response instanceof SchemaInterface)
			{
				$entityName = $response->getDefinition()->getName();
			}

			// create new operation
			$operation = new Operation($method->getName(), strtolower($method->getName()) . ucfirst($entityName));

			if($request instanceof SchemaInterface)
			{
				$type       = strtolower($method->getName()) . 'Request';
				$parameter  = new Parameter('body', 'body', null, true);
				$parameter->setType($type);

				$operation->addParameter($parameter);
			}

			$responses = $method->getResponses();

			foreach($responses as $statusCode => $response)
			{
				$type    = strtolower($method->getName()) . 'Response';
				$message = $response->getDefinition()->getDescription() ?: 'Response';

				$operation->addResponseMessage(new ResponseMessage($statusCode, $message, $type));
			}

			$api->addOperation($operation);
		}

		return array($api);
	}

	protected function getModels(Resource $resource)
	{
		$generator = new JsonSchema($this->targetNamespace);
		$data      = json_decode($generator->generate($resource));
		$models    = new \stdClass();

		$properties = (array) $data->properties;
		foreach($properties as $name => $property)
		{
			$description = isset($property->description) ? $property->description : null;
			$required    = isset($property->required) ? $property->required : null;
			$properties  = isset($property->properties) ? $property->properties : new \stdClass();

			$model = new Model($name, $description, $required);
			$model->setProperties($properties);

			$models->$name = $model;
		}

		$definitions = (array) $data->definitions;
		foreach($definitions as $name => $definition)
		{
			$description = isset($definition->description) ? $definition->description : null;
			$required    = isset($definition->required) ? $definition->required : null;
			$properties  = isset($definition->properties) ? $definition->properties : new \stdClass();

			$model = new Model($name, $description, $required);
			$model->setProperties($properties);

			$models->$name = $model;
		}

		return $models;
	}

	/**
	 * Tansforms an PSX route into an Swagger-Style route
	 *
	 * @param string $path
	 * @return string
	 */
	public static function transformRoute($path)
	{
		return preg_replace('/(\:|\*)(\w+)/i', '{$2}', $path);
	}
}

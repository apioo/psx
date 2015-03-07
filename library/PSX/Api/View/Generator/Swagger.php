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

namespace PSX\Api\View\Generator;

use PSX\Api\View;
use PSX\Api\View\GeneratorAbstract;
use PSX\Data\Writer\Json as JsonWriter;
use PSX\Json;
use PSX\Swagger\Api;
use PSX\Swagger\Declaration;
use PSX\Swagger\Model;
use PSX\Swagger\Operation;
use PSX\Swagger\Parameter;
use PSX\Swagger\ResponseMessage;

/**
 * Generates an Swagger 1.2 representation of an API view. Note this does not
 * generate a resource listing only the documentation of an single resource
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

	public function generate(View $view)
	{
		$declaration = new Declaration($this->apiVersion);
		$declaration->setBasePath($this->basePath);
		$declaration->setApis($this->getApis($view));
		$declaration->setModels($this->getModels($view));
		$declaration->setResourcePath(self::transformRoute($view->getPath()));

		$writer  = new JsonWriter();
		$swagger = $writer->write($declaration);

		// since swagger does not fully support the json schema spec we must 
		// remove the $ref fragments
		$swagger = str_replace('#\/definitions\/', '', $swagger);

		return $swagger;
	}

	protected function getApis(View $view)
	{
		$methods = View::getMethods();
		$api     = new Api(self::transformRoute($view->getPath()));

		foreach($methods as $method => $methodName)
		{
			if($view->has($method))
			{
				if($view->has($method | View::TYPE_REQUEST))
				{
					$entityName = $view->get($method | View::TYPE_REQUEST)->getDefinition()->getName();
				}
				else if($view->has($method | View::TYPE_RESPONSE))
				{
					$entityName = $view->get($method | View::TYPE_RESPONSE)->getDefinition()->getName();
				}

				$name      = strtolower($methodName) . ucfirst($entityName);
				$operation = new Operation($methodName, $name);

				if($view->has($method | View::TYPE_RESPONSE))
				{
					$definition = $view->get($method | View::TYPE_RESPONSE)->getDefinition();
					$type       = $this->getPrefix($method | View::TYPE_RESPONSE);
					$message    = $definition->getDescription() ?: 'Response';

					$operation->addResponseMessage(new ResponseMessage(200, $message, $type));
				}

				if($view->has($method | View::TYPE_REQUEST))
				{
					$definition = $view->get($method | View::TYPE_REQUEST)->getDefinition();
					$type       = $this->getPrefix($method | View::TYPE_REQUEST);
					$parameter  = new Parameter('body', 'body', null, true);
					$parameter->setType($type);

					$operation->addParameter($parameter);
				}

				$api->addOperation($operation);
			}
		}

		return array($api);
	}

	protected function getModels(View $view)
	{
		$generator = new JsonSchema($this->targetNamespace);
		$data      = json_decode($generator->generate($view), true);

		foreach($data['properties'] as $name => $property)
		{
			$description = isset($property['description']) ? $property['description'] : null;
			$required    = isset($property['required']) ? $property['required'] : null;
			$properties  = isset($property['properties']) ? $property['properties'] : array();

			$model = new Model($name, $description, $required);
			$model->setProperties($properties);

			$models[$name] = $model;
		}

		foreach($data['definitions'] as $name => $definition)
		{
			$description = isset($definition['description']) ? $definition['description'] : null;
			$required    = isset($definition['required']) ? $definition['required'] : null;
			$properties  = isset($definition['properties']) ? $definition['properties'] : array();

			$model = new Model($name, $description, $required);
			$model->setProperties($properties);

			$models[$name] = $model;
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

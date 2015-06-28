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
use PSX\Data\Schema\Generator\JsonSchema as JsonSchemaGenerator;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\SchemaInterface;
use PSX\Json;

/**
 * JsonSchema
 *
 * @see     http://tools.ietf.org/html/draft-zyp-json-schema-04
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchema extends GeneratorAbstract
{
	protected $targetNamespace;

	public function __construct($targetNamespace)
	{
		$this->targetNamespace = $targetNamespace;
	}

	public function generate(Resource $resource)
	{
		$schemas = array();
		$methods = $resource->getMethods();

		$definitions = array();
		$properties  = array();

		foreach($methods as $method)
		{
			$request = $method->getRequest();

			if($request instanceof SchemaInterface)
			{
				list($defs, $props) = $this->getJsonSchema($request);

				$key        = strtolower($method->getName()) . 'Request';
				$definitions = array_merge($definitions, $defs);

				if(isset($props['properties']))
				{
					$properties[$key] = $props;
				}
			}

			$response = $this->getSuccessfulResponse($method);

			if($response instanceof SchemaInterface)
			{
				list($defs, $props) = $this->getJsonSchema($response);

				$key         = strtolower($method->getName()) . 'Response';
				$definitions = array_merge($definitions, $defs);

				if(isset($props['properties']))
				{
					$properties[$key] = $props;
				}
			}
		}

		$result = array(
			'$schema'     => JsonSchemaGenerator::SCHEMA,
			'id'          => $this->targetNamespace,
			'type'        => 'object',
			'definitions' => $definitions,
			'properties'  => $properties,
		);

		return Json::encode($result, JSON_PRETTY_PRINT);
	}

	protected function getJsonSchema(SchemaInterface $schema)
	{
		$generator   = new JsonSchemaGenerator($this->targetNamespace);
		$data        = json_decode($generator->generate($schema), true);
		$definitions = array();
		$properties  = array();

		unset($data['$schema']);
		unset($data['id']);

		if(isset($data['definitions']))
		{
			$definitions = $data['definitions'];

			unset($data['definitions']);
		}

		return [$definitions, $data];
	}
}

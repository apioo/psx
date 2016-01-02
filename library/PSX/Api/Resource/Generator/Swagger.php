<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
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
        $api         = new Api(ApiGeneration::transformRoutePlaceholder($resource->getPath()));
        $description = $resource->getDescription();
        $methods     = $resource->getMethods();

        if (!empty($description)) {
            $api->setDescription($description);
        }

        foreach ($methods as $method) {
            // get operation name
            $request     = $method->getRequest();
            $response    = $this->getSuccessfulResponse($method);
            $description = $method->getDescription();
            $entityName  = '';

            if ($request instanceof SchemaInterface) {
                $entityName = $request->getDefinition()->getName();
            } elseif ($response instanceof SchemaInterface) {
                $entityName = $response->getDefinition()->getName();
            }

            // create new operation
            $operation = new Operation($method->getName(), strtolower($method->getName()) . ucfirst($entityName));

            if (!empty($description)) {
                $operation->setSummary($description);
            }

            // path parameter
            $parameters = $resource->getPathParameters()->getDefinition();

            foreach ($parameters as $parameter) {
                $param = new Parameter('path', $parameter->getName(), $parameter->getDescription(), $parameter->isRequired());

                $this->setParameterType($parameter, $param);

                $operation->addParameter($param);
            }

            // query parameter
            $parameters = $method->getQueryParameters()->getDefinition();

            foreach ($parameters as $parameter) {
                $param = new Parameter('query', $parameter->getName(), $parameter->getDescription(), $parameter->isRequired());

                $this->setParameterType($parameter, $param);

                $operation->addParameter($param);
            }

            // request body
            if ($request instanceof SchemaInterface) {
                $description = $request->getDefinition()->getDescription();
                $type        = $method->getName() . '-request';
                $parameter   = new Parameter('body', 'body', $description, true);
                $parameter->setType($type);

                $operation->addParameter($parameter);
            }

            // response body
            $responses = $method->getResponses();

            foreach ($responses as $statusCode => $response) {
                $type    = $method->getName() . '-' . $statusCode . '-response';
                $message = $response->getDefinition()->getDescription() ?: $statusCode . ' response';

                $operation->addResponseMessage(new ResponseMessage($statusCode, $message, $type));
            }

            $api->addOperation($operation);
        }

        return array($api);
    }

    protected function getModels(Resource $resource)
    {
        $generator = new JsonSchema($this->targetNamespace);
        $data      = $generator->toArray($resource);
        $models    = new \stdClass();

        if (isset($data['definitions']) && is_array($data['definitions'])) {
            foreach ($data['definitions'] as $name => $definition) {
                $properties  = isset($definition['properties'])  ? $definition['properties']  : null;
                $description = isset($definition['description']) ? $definition['description'] : null;
                $required    = isset($definition['required'])    ? $definition['required']    : null;

                // if the property has an ref to an definition resolve the ref
                if (isset($definition['$ref'])) {
                    $ref = str_replace('#/definitions/', '', $definition['$ref']);
                    if (isset($data['definitions'][$ref])) {
                        $properties  = isset($data['definitions'][$ref]['properties'])  ? $data['definitions'][$ref]['properties']  : null;
                        $description = isset($data['definitions'][$ref]['description']) ? $data['definitions'][$ref]['description'] : null;
                        $required    = isset($data['definitions'][$ref]['required'])    ? $data['definitions'][$ref]['required']    : null;
                    }
                }

                $model = new Model($name, $description, $required);
                $model->setProperties($properties);

                $models->$name = $model;
            }
        }

        return $models;
    }

    protected function setParameterType(PropertyInterface $parameter, Parameter $param)
    {
        switch (true) {
            case $parameter instanceof Property\IntegerType:
                $param->setType('integer');
                break;

            case $parameter instanceof Property\FloatType:
                $param->setType('number');
                break;

            case $parameter instanceof Property\BooleanType:
                $param->setType('boolean');
                break;

            case $parameter instanceof Property\DateType:
                $param->setType('string');
                $param->setFormat('date');
                break;

            case $parameter instanceof Property\DateTimeType:
                $param->setType('string');
                $param->setFormat('date-time');
                break;

            default:
                $param->setType('string');
                break;
        }

        $param->setDescription($parameter->getDescription());
        $param->setRequired($parameter->isRequired());

        if ($parameter instanceof Property\DecimalType) {
            $param->setMinimum($parameter->getMin());
            $param->setMaximum($parameter->getMax());
        } elseif ($parameter instanceof Property\StringType) {
            $param->setMinimum($parameter->getMinLength());
            $param->setMaximum($parameter->getMaxLength());
            $param->setEnum($parameter->getEnumeration());
        }
    }
}

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

namespace PSX\Api\Documentation\Parser;

use Doctrine\Common\Annotations\Reader;
use PSX\Annotation as Anno;
use PSX\Api\Documentation;
use PSX\Api\Documentation\ParserInterface;
use PSX\Api\Resource;
use PSX\Controller\SchemaApiAbstract;
use PSX\Data\Schema\Parser\JsonSchema;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\SchemaManagerInterface;
use PSX\Data\SchemaInterface;
use PSX\Util\ApiGeneration;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

/**
 * Annotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Annotation implements ParserInterface
{
    protected $annotationReader;
    protected $schemaManager;

    public function __construct(Reader $annotationReader, SchemaManagerInterface $schemaManager)
    {
        $this->annotationReader = $annotationReader;
        $this->schemaManager    = $schemaManager;
    }

    public function parse($schema, $path)
    {
        if (!$schema instanceof SchemaApiAbstract) {
            throw new RuntimeException('Must be a SchemaApiAbstract controller');
        }

        // we dont need to consider the path since if we are here the routing
        // mechanism has already selected the corect controller

        $resource = $this->parseResource($schema, $path);

        return new Documentation\Simple($resource);
    }

    protected function parseResource($object, $path)
    {
        $controller = new ReflectionObject($object);
        $resource   = new Resource(Resource::STATUS_ACTIVE, $path);
        $basePath   = dirname($controller->getFileName());

        $this->parsePathParameters($controller, $resource);

        $methods = [
            'GET'    => 'doGet', 
            'POST'   => 'doPost', 
            'PUT'    => 'doPut', 
            'DELETE' => 'doDelete', 
            'PATCH'  => 'doPatch'
        ];

        foreach ($methods as $httpMethod => $methodName) {
            $reflection = $controller->getMethod($methodName);
            $method     = Resource\Factory::getMethod($httpMethod);

            // if the method is defined in the SchemaApiAbstract controller we
            // have no implementation so skip
            if ($reflection->getDeclaringClass()->getName() == 'PSX\\Controller\\SchemaApiAbstract') {
                continue;
            }

            // @TODO parse description from the doc comment
            $description = null;
            if (!empty($description)) {
                $method->setDescription($description);
            }

            $this->parseQueryParameters($reflection, $method);
            $this->parseRequest($reflection, $method, $basePath);
            $this->parseResponses($reflection, $method, $basePath);

            $resource->addMethod($method);
        }

        return $resource;
    }

    protected function parsePathParameters(ReflectionClass $reflectionClass, Resource $resource)
    {
        $annotations = $this->annotationReader->getClassAnnotations($reflectionClass);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Anno\PathParam) {
                $resource->addPathParameter($this->getParameter($annotation));
            }
        }
    }

    protected function parseQueryParameters(ReflectionMethod $reflectionMethod, Resource\MethodAbstract $method)
    {
        $annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Anno\QueryParam) {
                $method->addQueryParameter($this->getParameter($annotation));
            }
        }
    }

    protected function parseRequest(ReflectionMethod $reflectionMethod, Resource\MethodAbstract $method, $basePath)
    {
        $annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Anno\Incoming) {
                $schema = $this->getBodySchema($annotation, $basePath);

                if ($schema instanceof SchemaInterface) {
                    $method->setRequest($schema);
                }
            }
        }
    }

    protected function parseResponses(ReflectionMethod $reflectionMethod, Resource\MethodAbstract $method, $basePath)
    {
        $annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Anno\Outgoing) {
                $schema = $this->getBodySchema($annotation, $basePath);

                if ($schema instanceof SchemaInterface) {
                    $method->addResponse($annotation->getCode(), $schema);
                }
            }
        }
    }

    protected function getBodySchema(Anno\SchemaAbstract $annotation, $basePath)
    {
        $schema = $annotation->getSchema();

        if (is_file($basePath . '/' . $schema)) {
            return JsonSchema::fromFile($basePath . '/' . $schema);
        } elseif (class_exists($schema)) {
            return $this->schemaManager->getSchema($schema);
        } else {
            throw new RuntimeException('Invalid schema source ' . $schema);
        }
    }

    protected function getParameter(Anno\ParamAbstract $param)
    {
        $property = $this->getPropertyType($param->getType(), $param->getName());

        $description = $param->getDescription();
        if ($description !== null) {
            $property->setDescription($description);
        }

        $required = $param->getRequired();
        if ($required !== null) {
            $property->setRequired((bool) $required);
        }

        $enum = $param->getEnum();
        if ($enum !== null && is_array($enum)) {
            $property->setEnumeration($enum);
        }

        $pattern = $param->getPattern();
        if ($pattern !== null) {
            $property->setPattern($pattern);
        }

        return $property;
    }

    protected function getPropertyType($type, $name)
    {
        switch ($type) {
            case 'integer':
                return Property::getInteger($name);

            case 'number':
                return Property::getFloat($name);

            case 'date':
                return Property::getDateTime($name);

            case 'boolean':
                return Property::getBoolean($name);

            case 'string':
            default:
                return Property::getString($name);
        }
    }
}

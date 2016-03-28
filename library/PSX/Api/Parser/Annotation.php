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

namespace PSX\Api\Parser;

use Doctrine\Common\Annotations\Reader;
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\Annotation as Anno;
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaManagerInterface;
use ReflectionObject;
use RuntimeException;

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
    protected $resources;

    public function __construct(Reader $annotationReader, SchemaManagerInterface $schemaManager)
    {
        $this->annotationReader = $annotationReader;
        $this->schemaManager    = $schemaManager;
    }

    public function parse($schema, $path)
    {
        if (!is_object($schema)) {
            throw new RuntimeException('Schema must be an object');
        }

        $resource    = new Resource(Resource::STATUS_ACTIVE, $path);
        $controller  = new ReflectionObject($schema);
        $basePath    = dirname($controller->getFileName());
        $annotations = $this->annotationReader->getClassAnnotations($controller);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Anno\Title) {
                $resource->setTitle($annotation->getTitle());
            } elseif ($annotation instanceof Anno\Description) {
                $resource->setDescription($annotation->getDescription());
            } elseif ($annotation instanceof Anno\PathParam) {
                $resource->addPathParameter($this->getParameter($annotation));
            }
        }

        $this->parseMethods($controller, $resource, $basePath);

        return $resource;
    }

    protected function parseMethods(ReflectionObject $controller, Resource $resource, $basePath)
    {
        $methods = [
            'GET'    => 'doGet', 
            'POST'   => 'doPost', 
            'PUT'    => 'doPut', 
            'DELETE' => 'doDelete', 
            'PATCH'  => 'doPatch'
        ];

        foreach ($methods as $httpMethod => $methodName) {
            // check whether method exists
            if (!$controller->hasMethod($methodName)) {
                continue;
            }

            $method      = Resource\Factory::getMethod($httpMethod);
            $reflection  = $controller->getMethod($methodName);
            $annotations = $this->annotationReader->getMethodAnnotations($reflection);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Anno\Description) {
                    $method->setDescription($annotation->getDescription());
                } elseif ($annotation instanceof Anno\QueryParam) {
                    $method->addQueryParameter($this->getParameter($annotation));
                } elseif ($annotation instanceof Anno\Incoming) {
                    $schema = $this->getBodySchema($annotation, $basePath);
                    if ($schema instanceof SchemaInterface) {
                        $method->setRequest($schema);
                    }
                } elseif ($annotation instanceof Anno\Outgoing) {
                    $schema = $this->getBodySchema($annotation, $basePath);
                    if ($schema instanceof SchemaInterface) {
                        $method->addResponse($annotation->getCode(), $schema);
                    }
                } elseif ($annotation instanceof Anno\Exclude) {
                    // skip this method
                    continue 2;
                }
            }

            $resource->addMethod($method);
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

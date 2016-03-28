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

use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\Util\Inflection;
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\SchemaInterface;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

/**
 * Raml
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
class Raml implements ParserInterface
{
    protected $basePath;
    protected $parser;
    protected $data;

    public function __construct($basePath = null, Parser $parser = null)
    {
        $this->basePath = $basePath;
        $this->parser   = $parser ?: new Parser();
    }

    public function parse($schema, $path)
    {
        $this->data = $this->parser->parse($schema);

        $path = Inflection::transformRoutePlaceholder($path);

        if (isset($this->data[$path]) && is_array($this->data[$path])) {
            return $this->parseResource($this->data[$path], $path);
        } else {
            // we check whether the path is nested
            $parts = explode('/', trim($path, '/'));
            $data  = $this->data;

            foreach ($parts as $part) {
                if (isset($data['/' . $part])) {
                    $data = $data['/' . $part];
                } else {
                    $data = null;
                    break;
                }
            }

            if (!empty($data) && is_array($data)) {
                return $this->parseResource($data, $path);
            } else {
                throw new RuntimeException('Could not find resource definition "' . $path . '" in RAML schema');
            }
        }
    }

    protected function parseResource(array $data, $path)
    {
        $status = Resource::STATUS_ACTIVE;
        if (isset($this->data['status'])) {
            $status = $this->getResourceStatus($this->data['status']);
        }

        $resource = new Resource($status, $path);

        if (isset($data['displayName'])) {
            $resource->setTitle($data['displayName']);
        }

        if (isset($data['description'])) {
            $resource->setDescription($data['description']);
        }

        $this->parseUriParameters($resource, $data);

        $mergedTrait = array();
        if (isset($data['is']) && is_array($data['is'])) {
            foreach ($data['is'] as $traitName) {
                $trait = $this->getTrait($traitName);
                if (is_array($trait)) {
                    $mergedTrait = array_merge_recursive($mergedTrait, $trait);
                }
            }
        }

        foreach ($data as $methodName => $row) {
            if (in_array($methodName, ['get', 'post', 'put', 'delete']) && is_array($row)) {
                if (!empty($mergedTrait)) {
                    $row = array_merge_recursive($row, $mergedTrait);
                }

                $method = Resource\Factory::getMethod(strtoupper($methodName));

                if (isset($row['description'])) {
                    $method->setDescription($row['description']);
                }

                $this->parseQueryParameters($method, $row);
                $this->parseRequest($method, $row);
                $this->parseResponses($method, $row);

                $resource->addMethod($method);
            }
        }

        return $resource;
    }

    protected function getTrait($name)
    {
        if (isset($this->data['traits']) && is_array($this->data['traits'])) {
            foreach ($this->data['traits'] as $trait) {
                if (is_array($trait) && isset($trait[$name])) {
                    return $this->parseDefinition($trait[$name]);
                }
            }
        }

        return null;
    }

    protected function getSchema($name)
    {
        if (isset($this->data['schemas']) && is_array($this->data['schemas'])) {
            foreach ($this->data['schemas'] as $schema) {
                if (is_array($schema) && isset($schema[$name])) {
                    return $this->parseSchema($schema[$name]);
                }
            }
        }

        return null;
    }

    protected function parseUriParameters(Resource $resource, array $data)
    {
        if (isset($data['uriParameters']) && is_array($data['uriParameters'])) {
            foreach ($data['uriParameters'] as $name => $definition) {
                if (!empty($name) && is_array($definition)) {
                    $resource->addPathParameter($this->getParameter($name, $definition));
                }
            }
        }
    }

    protected function parseQueryParameters(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['queryParameters']) && is_array($data['queryParameters'])) {
            foreach ($data['queryParameters'] as $name => $definition) {
                if (!empty($name) && is_array($definition)) {
                    $method->addQueryParameter($this->getParameter($name, $definition));
                }
            }
        }
    }

    protected function getParameter($name, array $definition)
    {
        $type     = isset($definition['type']) ? $definition['type'] : 'string';
        $property = $this->getPropertyType($type, $name);

        if (isset($definition['description'])) {
            $property->setDescription($definition['description']);
        }

        if (isset($definition['required'])) {
            $property->setRequired((bool) $definition['required']);
        }

        if (isset($definition['enum']) && is_array($definition['enum'])) {
            $property->setEnumeration($definition['enum']);
        }

        if (isset($definition['pattern'])) {
            $property->setPattern($definition['pattern']);
        }

        if (isset($definition['minLength'])) {
            $property->setMinLength($definition['minLength']);
        }

        if (isset($definition['maxLength'])) {
            $property->setMaxLength($definition['maxLength']);
        }

        if (isset($definition['minimum'])) {
            $property->setMin($definition['minimum']);
        }

        if (isset($definition['maximum'])) {
            $property->setMax($definition['maximum']);
        }

        return $property;
    }

    protected function parseRequest(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['body']) && is_array($data['body'])) {
            $schema = $this->getBodySchema($data['body']);

            if ($schema instanceof SchemaInterface) {
                $method->setRequest($schema);
            }
        }
    }

    protected function parseResponses(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['responses']) && is_array($data['responses'])) {
            foreach ($data['responses'] as $statusCode => $row) {
                if (isset($row['body']) && is_array($row['body'])) {
                    $schema = $this->getBodySchema($row['body']);

                    if ($schema instanceof SchemaInterface) {
                        $method->addResponse($statusCode, $schema);
                    }
                }
            }
        }
    }

    protected function getBodySchema(array $body)
    {
        foreach ($body as $contentType => $row) {
            if ($contentType == 'application/json' && isset($row['schema']) && is_string($row['schema'])) {
                if (ctype_alnum($row['schema'])) {
                    $schema = $this->getSchema($row['schema']);

                    if ($schema instanceof SchemaInterface) {
                        return $schema;
                    } else {
                        throw new RuntimeException('Referenced schema "' . $row['schema'] . '" does not exist');
                    }
                } else {
                    return $this->parseSchema($row['schema']);
                }
            }
        }

        return null;
    }

    protected function parseSchema($schema)
    {
        if (is_string($schema)) {
            if (substr($schema, 0, 8) == '!include') {
                $file = trim(substr($schema, 8));
                if (!is_file($file)) {
                    $file = $this->basePath . '/' . $file;
                }

                return JsonSchema::fromFile($file);
            } else {
                $parser = new JsonSchema($this->basePath);

                return $parser->parse($schema);
            }
        } else {
            throw new RuntimeException('Schema definition must be a string');
        }
    }

    protected function parseDefinition($definition)
    {
        if (is_string($definition) && substr($definition, 0, 8) == '!include') {
            $file = trim(substr($definition, 8));

            if (!is_file($file)) {
                $file = $this->basePath !== null ? $this->basePath . DIRECTORY_SEPARATOR . $file : $file;
            }

            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if (in_array($extension, ['raml', 'yml', 'yaml'])) {
                return $this->parser->parse(file_get_contents($file));
            } else {
                return file_get_contents($file);
            }
        } else {
            return $definition;
        }
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

    protected function getResourceStatus($status)
    {
        if ($status === 'deprecated') {
            return Resource::STATUS_DEPRECATED;
        } elseif ($status === 'development') {
            return Resource::STATUS_DEVELOPMENT;
        } elseif ($status === 'closed') {
            return Resource::STATUS_CLOSED;
        } else {
            return Resource::STATUS_ACTIVE;
        }
    }

    public static function fromFile($file, $path)
    {
        if (!empty($file) && is_file($file)) {
            $basePath = pathinfo($file, PATHINFO_DIRNAME);
            $parser   = new Raml($basePath);

            return $parser->parse(file_get_contents($file), $path);
        } else {
            throw new RuntimeException('Could not load RAML schema ' . $file);
        }
    }
}

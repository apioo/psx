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

namespace PSX\Schema\Parser\JsonSchema;

use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertySimpleAbstract;
use PSX\Json;
use PSX\Json\Pointer;
use PSX\Uri\Uri;
use RuntimeException;

/**
 * Document
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Document
{
    protected $data;
    protected $resolver;
    protected $basePath;
    protected $source;
    protected $baseUri;

    public function __construct(array $data, RefResolver $resolver, $basePath = null, Uri $source = null)
    {
        $this->assertVersion($data);

        $this->data     = $data;
        $this->resolver = $resolver;
        $this->basePath = $basePath;
        $this->source   = $source;
        $this->baseUri  = new Uri(isset($data['id']) ? $data['id'] : 'http://phpsx.org/2015/data#');
    }

    /**
     * The base path if the schema was fetched from a file
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Returns the source from where the document was obtained
     *
     * @return \PSX\Uri\Uri
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns whether the document was fetched from a remote or local source
     *
     * @return boolean
     */
    public function isRemote()
    {
        return $this->source !== null && in_array($this->source->getScheme(), ['http', 'https']);
    }

    /**
     * @return \PSX\Uri\Uri
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param string $pointer
     * @param string $name
     * @param integer $depth
     * @return \PSX\Schema\PropertyInterface
     */
    public function getProperty($pointer = null, $name = null, $depth = 0)
    {
        if ($pointer === null) {
            return $this->getRecProperty($this->data, $name, $depth);
        } else {
            return $this->getRecProperty($this->pointer($pointer), $name, $depth);
        }
    }

    /**
     * Resolves a json pointer on the document and returns the fitting array
     * fragment. Throws an exception if the pointer could not be resolved
     *
     * @param string $pointer
     * @return array
     */
    public function pointer($pointer)
    {
        $pointer = new Pointer($pointer);
        $data    = $pointer->evaluate($this->data);

        if ($data !== null) {
            return $data;
        } else {
            throw new RuntimeException('Could not resolve pointer ' . $pointer->getPath());
        }
    }

    /**
     * @param \PSX\Uri\Uri $ref
     * @return boolean
     */
    public function canResolve(Uri $ref)
    {
        return $this->baseUri->getHost() == $ref->getHost() && $this->baseUri->getPath() == $ref->getPath();
    }

    protected function getRecProperty(array $data, $name, $depth)
    {
        if (isset($data['$ref'])) {
            return $this->resolver->resolve($this, new Uri($data['$ref']), $name, $depth);
        }

        if (isset($data['extends'])) {
            $part = $this->resolver->extract($this, new Uri($data['extends']));
            $data = array_replace_recursive($data, $part);
        }

        if (empty($name)) {
            $name = isset($data['title']) ? $data['title'] : null;
        }

        if (isset($data['oneOf']) && is_array($data['oneOf'])) {
            return $this->parseOneOf($data, $name, $depth);
        }

        $type = isset($data['type']) ? $data['type'] : null;

        switch ($type) {
            case 'object':
                return $this->parseComplexType($data, $name, $depth);
                break;

            case 'array':
                return $this->parseArrayType($data, $name, $depth);
                break;

            case 'boolean':
                return $this->parseBoolean($data, $name);
                break;

            case 'integer':
                return $this->parseInteger($data, $name);
                break;

            case 'number':
                return $this->parseFloat($data, $name);
                break;

            default:
            case 'string':
                return $this->parseString($data, $name);
                break;
        }
    }

    protected function parseComplexType(array $data, $name, $depth)
    {
        $complexType = Property::getComplex($name);
        $properties  = isset($data['properties']) ? $data['properties'] : array();

        if (isset($data['patternProperties']) && is_array($data['patternProperties'])) {
            $prototype = current($data['patternProperties']);

            if (!empty($prototype) && is_array($prototype)) {
                $anyType = Property::getAny($name);
                $anyType->setPrototype($this->getRecProperty($prototype, $name, $depth + 1));

                return $anyType;
            }
        }

        foreach ($properties as $name => $row) {
            if (is_array($row)) {
                $property = $this->getRecProperty($row, $name, $depth + 1);

                if ($property !== null) {
                    $complexType->add($property);
                }
            }
        }

        if (isset($data['description'])) {
            $complexType->setDescription($data['description']);
        }

        if (isset($data['required']) && is_array($data['required'])) {
            foreach ($data['required'] as $propertyName) {
                $property = $complexType->get($propertyName);

                if ($property instanceof PropertyInterface) {
                    $property->setRequired(true);
                }
            }
        }

        if (isset($data['reference']) && class_exists($data['reference'])) {
            $complexType->setReference($data['reference']);
        }

        return $complexType;
    }

    protected function parseArrayType(array $data, $name, $depth)
    {
        $arrayType = Property::getArray($name);

        if (isset($data['minItems'])) {
            $arrayType->setMinLength($data['minItems']);
        }

        if (isset($data['maxItems'])) {
            $arrayType->setMaxLength($data['maxItems']);
        }

        if (isset($data['items']) && is_array($data['items'])) {
            $property = $this->getRecProperty($data['items'], null, $depth + 1);

            if ($property !== null) {
                $arrayType->setPrototype($property);
            }
        }

        if (isset($data['description'])) {
            $arrayType->setDescription($data['description']);
        }

        return $arrayType;
    }

    protected function parseBoolean(array $data, $name)
    {
        $property = Property::getBoolean($name);

        $this->parseScalar($property, $data);

        return $property;
    }

    protected function parseInteger(array $data, $name)
    {
        $property = Property::getInteger($name);

        $this->parseScalar($property, $data);

        return $property;
    }

    protected function parseFloat(array $data, $name)
    {
        $property = Property::getFloat($name);

        $this->parseScalar($property, $data);

        return $property;
    }

    protected function parseString(array $data, $name)
    {
        $property = null;
        if (isset($data['format'])) {
            if ($data['format'] == 'date') {
                $property = Property::getDate($name);
            } elseif ($data['format'] == 'date-time') {
                $property = Property::getDateTime($name);
            } elseif ($data['format'] == 'duration') {
                $property = Property::getDuration($name);
            } elseif ($data['format'] == 'time') {
                $property = Property::getTime($name);
            }
        }

        if ($property === null) {
            $property = Property::getString($name);
        }

        $this->parseScalar($property, $data);

        return $property;
    }

    protected function parseScalar(PropertySimpleAbstract $property, array $data)
    {
        if (isset($data['pattern'])) {
            $property->setPattern($data['pattern']);
        }

        if (isset($data['enum'])) {
            $property->setEnumeration($data['enum']);
        }

        if (isset($data['minimum'])) {
            $property->setMin($data['minimum']);
        }

        if (isset($data['maximum'])) {
            $property->setMax($data['maximum']);
        }

        if (isset($data['minLength'])) {
            $property->setMinLength($data['minLength']);
        }

        if (isset($data['maxLength'])) {
            $property->setMaxLength($data['maxLength']);
        }

        if (isset($data['description'])) {
            $property->setDescription($data['description']);
        }
    }

    protected function parseOneOf(array $data, $name, $depth)
    {
        $choiceType = Property::getChoice($name);

        foreach ($data['oneOf'] as $row) {
            $property = $this->getRecProperty($row, null, $depth);

            if ($property instanceof Property\ComplexType) {
                $choiceType->add($property);
            }
        }

        return $choiceType;
    }

    protected function assertVersion(array $data)
    {
        if (isset($data['$schema']) && $data['$schema'] != JsonSchema::SCHEMA_04) {
            throw new UnsupportedVersionException('Invalid version requires ' . JsonSchema::SCHEMA_04);
        }
    }
}

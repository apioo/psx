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

namespace PSX\Data\Schema\Generator;

use PSX\Data\Schema\GeneratorInterface;
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
class JsonSchema implements GeneratorInterface
{
    const SCHEMA = 'http://json-schema.org/draft-04/schema#';

    protected $targetNamespace;
    protected $definitions;

    public function __construct($targetNamespace = null)
    {
        $this->targetNamespace = $targetNamespace ?: 'urn:schema.phpsx.org#';
    }

    public function generate(SchemaInterface $schema)
    {
        return Json::encode($this->toArray($schema), JSON_PRETTY_PRINT);
    }

    /**
     * Returns the jsonschema as array
     *
     * @return array
     */
    public function toArray(SchemaInterface $schema)
    {
        return $this->generateRootElement($schema->getDefinition());
    }

    protected function generateRootElement(Property\ComplexType $type)
    {
        $properties  = $type->getProperties();
        $typeName    = $type->getName();
        $description = $type->getDescription();
        $props       = array();
        $required    = array();

        $this->definitions = array();

        foreach ($properties as $name => $property) {
            $props[$name] = $this->generateType($property);

            if ($property->isRequired()) {
                $required[] = $name;
            }
        }
        
        $definitions = array();
        foreach ($this->definitions as $name => $type) {
            $definitions[$name] = $type;
        }

        $result = array(
            '$schema' => self::SCHEMA,
            'id'      => $this->targetNamespace,
            'type'    => 'object',
        );

        if (!empty($typeName)) {
            $result['title'] = $typeName;
        }

        if (!empty($description)) {
            $result['description'] = $description;
        }

        if (!empty($definitions)) {
            $result['definitions'] = $definitions;
        }

        if (!empty($props)) {
            $result['properties'] = $props;
        }

        if (!empty($required)) {
            $result['required'] = $required;
        }

        $result['additionalProperties'] = false;

        return $result;
    }

    protected function generateType(PropertyInterface $type)
    {
        if ($type instanceof Property\AnyType) {
            $result = array(
                'type' => 'object',
            );

            $name = $type->getName();
            if (!empty($name)) {
                $result['title'] = $name;
            }

            $description = $type->getDescription();
            if (!empty($description)) {
                $result['description'] = $description;
            }

            $result['patternProperties'] = array(
                '^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$' => $this->generateType($type->getPrototype())
            );

            $result['additionalProperties'] = false;

            return $this->generateRef($type, $result);
        } elseif ($type instanceof Property\ArrayType) {
            $result = array(
                'type'  => 'array',
                'items' => $this->generateType($type->getPrototype()),
            );

            $name = $type->getName();
            if (!empty($name)) {
                $result['title'] = $name;
            }

            $description = $type->getDescription();
            if (!empty($description)) {
                $result['description'] = $description;
            }

            $minLength = $type->getMinLength();
            if ($minLength) {
                $result['minItems'] = $minLength;
            }

            $maxLength = $type->getMaxLength();
            if ($maxLength) {
                $result['maxItems'] = $maxLength;
            }

            return $result;
        } elseif ($type instanceof Property\ChoiceType) {
            $properties = $type->getProperties();
            $props      = array();

            foreach ($properties as $property) {
                $props[] = $this->generateType($property);
            }

            $result = array(
                'oneOf' => $props,
            );

            $name = $type->getName();
            if (!empty($name)) {
                $result['title'] = $name;
            }

            $description = $type->getDescription();
            if (!empty($description)) {
                $result['description'] = $description;
            }

            return $this->generateRef($type, $result);
        } elseif ($type instanceof Property\ComplexType) {
            $properties = $type->getProperties();
            $props      = array();
            $required   = array();

            foreach ($properties as $property) {
                $props[$property->getName()] = $this->generateType($property);

                if ($property->isRequired()) {
                    $required[] = $property->getName();
                }
            }

            $result = array(
                'type'       => 'object',
                'properties' => $props,
            );

            $name = $type->getName();
            if (!empty($name)) {
                $result['title'] = $name;
            }

            $description = $type->getDescription();
            if (!empty($description)) {
                $result['description'] = $description;
            }

            if (!empty($required)) {
                $result['required'] = $required;
            }

            $result['additionalProperties'] = false;

            return $this->generateRef($type, $result);
        } else {
            $result = array();
            $result['type'] = $this->getPropertyTypeName($type);

            $description = $type->getDescription();
            if (!empty($description)) {
                $result['description'] = $description;
            }

            if ($type instanceof Property\StringType) {
                $minLength = $type->getMinLength();
                if ($minLength) {
                    $result['minLength'] = $minLength;
                }

                $maxLength = $type->getMaxLength();
                if ($maxLength) {
                    $result['maxLength'] = $maxLength;
                }

                $pattern = $type->getPattern();
                if ($pattern) {
                    $result['pattern'] = $pattern;
                }
            } elseif ($type instanceof Property\DecimalType) {
                $min = $type->getMin();
                if ($min) {
                    $result['minimum'] = $min;
                }

                $max = $type->getMax();
                if ($max) {
                    $result['maximum'] = $max;
                }
            }

            $enumeration = $type->getEnumeration();
            if ($enumeration) {
                $result['enum'] = $enumeration;
            }

            return $result;
        }
    }

    protected function getPropertyTypeName(PropertyInterface $type)
    {
        switch ($type->getTypeName()) {
            case 'float':
                return 'number';

            case 'integer':
                return 'integer';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    protected function generateRef(PropertyInterface $type, array $result)
    {
        $key = 'ref' . $type->getId();

        $this->definitions[$key] = $result;

        return ['$ref' => '#/definitions/' . $key];
    }
}

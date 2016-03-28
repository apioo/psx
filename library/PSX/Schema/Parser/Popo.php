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

namespace PSX\Schema\Parser;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use PSX\Framework\Util\Annotation;
use PSX\Schema\Parser\Popo\ObjectReader;
use PSX\Schema\ParserInterface;
use PSX\Schema\Property;
use PSX\Schema\PropertyAbstract;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertySimpleAbstract;
use PSX\Schema\Schema;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * Tries to import the data into a plain old php object
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Popo implements ParserInterface
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * Holds all parsed objects to reuse
     *
     * @var array
     */
    protected $objects;

    /**
     * Contains the current path to detect recursions
     *
     * @var array
     */
    protected $stack;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function parse($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException('Class name must be a string');
        }

        $this->objects = [];
        $this->stack   = [];

        $object = new Property\ComplexType('record');
        $object->setReference($className);

        $this->parseComplexType($object);

        return new Schema($object);
    }

    /**
     * @param string $type
     * @param string $key
     * @return \PSX\Schema\PropertyInterface
     */
    protected function getProperty($type, $key, ReflectionProperty $reflection)
    {
        if (empty($type)) {
            $type = 'string';
        }

        if (($property = $this->findProperty($type, $reflection)) !== null) {
            return $property;
        }

        $pos = strpos($type, '<');
        if ($pos !== false) {
            $typeHint = substr($type, $pos + 1, strrpos($type, '>') - $pos - 1);
            $typeHint = ltrim($typeHint, '\\');
            $baseType = substr($type, 0, $pos);
        } else {
            $typeHint = null;
            $baseType = $type;
        }

        $baseType = strtolower(ltrim($baseType, '\\'));

        switch ($baseType) {
            case 'any':
            case 'map':
                $property = new Property\AnyType($key);
                $property->setPrototype($this->getProperty($typeHint, $key, $reflection));
                break;

            case 'array':
                $property = new Property\ArrayType($key);
                $property->setPrototype($this->getProperty($typeHint, $key, $reflection));
                break;

            case 'bool':
            case 'boolean':
                $property = new Property\BooleanType($key);
                break;

            case 'choice':
                $property = new Property\ChoiceType($key);
                $property->setReference($typeHint);

                $this->parseChoiceType($property, $reflection);
                break;

            case 'datetime':
                $property = new Property\DateTimeType($key);
                if (!empty($typeHint)) {
                    $property->setPattern($this->getDateTimePattern($typeHint));
                }
                break;

            case 'date':
                $property = new Property\DateType($key);
                break;

            case 'duration':
                $property = new Property\DurationType($key);
                break;

            case 'float':
                $property = new Property\FloatType($key);
                break;

            case 'int':
            case 'integer':
                $property = new Property\IntegerType($key);
                break;

            case 'string':
                $property = new Property\StringType($key);
                break;

            case 'time':
                $property = new Property\TimeType($key);
                break;

            case 'complex':
            default:
                $property = new Property\ComplexType($key);
                $property->setReference($type == 'complex' ? $typeHint : $type);

                $this->addObjectCache($type, $reflection, $property);
                $this->pushProperty($type, $reflection, $property);

                $this->parseComplexType($property);

                $this->popProperty();
                break;
        }

        $this->parseProperties($reflection, $property);

        if ($property instanceof Property\ArrayType) {
            $this->parseArrayProperties($reflection, $property);
        } elseif ($property instanceof Property\DecimalType) {
            $this->parseDecimalProperties($reflection, $property);
        } elseif ($property instanceof Property\StringType) {
            $this->parseStringProperties($reflection, $property);
        }

        if ($property instanceof PropertySimpleAbstract) {
            $this->parseSimpleProperties($reflection, $property);
        }

        return $property;
    }

    protected function parseChoiceType(Property\ChoiceType $property, ReflectionProperty $reflection)
    {
        $class = new ReflectionClass($property->getReference());
        $types = $class->newInstance()->getTypes();

        foreach ($types as $key => $type) {
            $property->add($this->getProperty($type, $key, $reflection));
        }
    }

    protected function parseComplexType(Property\ComplexType $property)
    {
        $class = new ReflectionClass($property->getReference());

        $description = $this->reader->getClassAnnotation($class, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Description');
        if ($description !== null) {
            $property->setDescription($description->getDescription());
        }

        $additionalProperties = $this->reader->getClassAnnotation($class, 'PSX\\Schema\\Parser\\Popo\\Annotation\\AdditionalProperties');
        if ($additionalProperties !== null) {
            $property->setDescription($additionalProperties->hasAdditionalProperties());
        }

        $properties = ObjectReader::getProperties($this->reader, $class);
        foreach ($properties as $key => $reflection) {
            $type = $this->getTypeForProperty($reflection);
            $prop = $this->getProperty($type, $key, $reflection);

            if ($prop instanceof PropertyInterface) {
                $property->add($prop);
            }
        }
    }

    protected function parseArrayProperties(ReflectionProperty $reflection, Property\ArrayType $property)
    {
        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\MinLength');
        if ($annotation !== null) {
            $property->setMinLength($annotation->getMinLength());
        }

        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\MaxLength');
        if ($annotation !== null) {
            $property->setMaxLength($annotation->getMaxLength());
        }
    }

    protected function parseDecimalProperties(ReflectionProperty $reflection, Property\DecimalType $property)
    {
        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Minimum');
        if ($annotation !== null) {
            $property->setMin($annotation->getMin());
        }

        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Maximum');
        if ($annotation !== null) {
            $property->setMax($annotation->getMax());
        }
    }

    protected function parseStringProperties(ReflectionProperty $reflection, Property\StringType $property)
    {
        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\MinLength');
        if ($annotation !== null) {
            $property->setMinLength($annotation->getMinLength());
        }

        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\MaxLength');
        if ($annotation !== null) {
            $property->setMaxLength($annotation->getMaxLength());
        }
    }

    protected function parseSimpleProperties(ReflectionProperty $reflection, PropertySimpleAbstract $property)
    {
        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Pattern');
        if ($annotation !== null) {
            $property->setPattern($annotation->getPattern());
        }

        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Enum');
        if ($annotation !== null) {
            $property->setEnumeration($annotation->getEnum());
        }
    }

    protected function parseProperties(ReflectionProperty $reflection, PropertyAbstract $property)
    {
        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Key');
        if ($annotation !== null) {
            $property->setName($annotation->getKey());
        }

        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Description');
        if ($annotation !== null) {
            $property->setDescription($annotation->getDescription());
        }

        $annotation = $this->reader->getPropertyAnnotation($reflection, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Required');
        $property->setRequired($annotation !== null);
    }

    protected function getTypeForProperty(ReflectionProperty $property)
    {
        $annotation = $this->reader->getPropertyAnnotation($property, 'PSX\\Schema\\Parser\\Popo\\Annotation\\Type');

        if ($annotation !== null) {
            $type = $annotation->getType();
        } else {
            // as fallback we try to read the @var annotation
            preg_match('/\* @var (.*)\\s/imsU', $property->getDocComment(), $matches);
            if (isset($matches[1])) {
                $type = $matches[1];
            } else {
                $type = null;
            }
        }

        return $type;
    }

    /**
     * This method returns the datetime pattern of a specific name. This format
     * gets written into the pattern property. Since these format are no valid
     * regexp it would be great to convert the formats to the fitting regexp and
     * in the validation visitor back to the date time format so that we would
     * produce valid JSON schema patterns
     *
     * @param string $typeHint
     * @return string
     */
    protected function getDateTimePattern($typeHint)
    {
        switch ($typeHint) {
            case 'COOKIE':
                return \DateTime::COOKIE;
                break;

            case 'ISO8601':
                return \DateTime::ISO8601;
                break;

            case 'RFC822':
            case 'RFC1036':
            case 'RFC1123':
            case 'RFC2822':
                return \DateTime::RFC2822;
                break;

            case 'RFC850':
                return \DateTime::RFC850;
                break;

            case 'RSS':
                return \DateTime::RSS;
                break;

            case 'ATOM':
            case 'RFC3339':
            case 'W3C':
            default:
                return \DateTime::W3C;
                break;
        }
    }

    protected function findProperty($type, ReflectionProperty $reflection)
    {
        $id = $reflection->getDeclaringClass()->getName() . '::' . $reflection->getName() . '{' . $type . '}';

        if (isset($this->stack[$id])) {
            return new Property\RecursionType($this->stack[$id]);
        }

        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        return null;
    }

    protected function addObjectCache($type, ReflectionProperty $reflection, PropertyInterface $property)
    {
        $this->objects[$reflection->getDeclaringClass()->getName() . '::' . $reflection->getName() . '{' . $type . '}'] = $property;
    }

    protected function pushProperty($type, ReflectionProperty $reflection, PropertyInterface $property)
    {
        $this->stack[$reflection->getDeclaringClass()->getName() . '::' . $reflection->getName() . '{' . $type . '}'] = $property;
    }

    protected function popProperty()
    {
        array_pop($this->stack);
    }
}

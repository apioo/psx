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

namespace PSX\Schema\Visitor;

use PSX\Data\Record;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertySimpleAbstract;
use PSX\Schema\RevealerInterface;
use PSX\Schema\ValidationException;
use PSX\DateTime\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Schema\VisitorInterface;
use PSX\Validate\ValidatorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * IncomingVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class IncomingVisitor implements VisitorInterface
{
    /**
     * @var \PSX\Schema\RevealerInterface
     */
    protected $revealer;

    /**
     * @var \PSX\Validate\ValidatorInterface
     */
    protected $validator;

    /**
     * Sets an optional revealer which can return different object types for
     * specific data
     *
     * @param \PSX\Schema\RevealerInterface $revealer
     */
    public function setRevealer(RevealerInterface $revealer)
    {
        $this->revealer = $revealer;
    }

    /**
     * Sets an optional validator which can validate each value through custom
     * filters. This should only be used for filters which can not be defined
     * inside a JsonSchema like i.e. check whether a row exists in a database
     *
     * @param \PSX\Validate\ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function visitArray(array $data, Property\ArrayType $property, $path)
    {
        $this->assertCompositeProperties($property, $path);

        $this->assertArrayConstraints($data, $property, $path);

        $this->assertValidatorConstraints($data, $path);

        return $this->createSimpleProperty($data, $property);
    }

    public function visitBoolean($data, Property\BooleanType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if (is_bool($data)) {
        } elseif (is_scalar($data)) {
            $result = preg_match('/^true|false|1|0$/', $data);

            if ($result) {
                $data = $data === 'false' ? false : (bool) $data;
            } else {
                throw new ValidationException($path . ' must be a boolean format (true|false|1|0)');
            }
        } else {
            throw new ValidationException($path . ' must be a boolean format (true|false|1|0)');
        }

        $this->assertValidatorConstraints($data, $path);

        $data = $data === 'false' ? false : (bool) $data;

        return $this->createSimpleProperty($data, $property);
    }

    public function visitComplex(\stdClass $data, Property\ComplexType $property, $path)
    {
        $this->assertCompositeProperties($property, $path);

        $this->assertValidatorConstraints($data, $path);

        $reference = $property->getReference();
        if (!empty($reference)) {
            // if we have an explicit provided revealer
            if ($this->revealer !== null) {
                $object = $this->revealer->reveal($data, $path);
                if ($object === null) {
                } elseif (is_string($object)) {
                    $reference = $object;
                } elseif (is_object($object)) {
                    return $object;
                } else {
                    throw new RuntimeException('Revealer must return either a string, object or null');
                }
            }

            $class  = new ReflectionClass($reference);
            $record = $class->newInstance();

            foreach ($data as $key => $val) {
                try {
                    $methodName = 'set' . ucfirst($key);
                    $method     = $class->getMethod($methodName);

                    if ($method instanceof ReflectionMethod) {
                        $method->invokeArgs($record, array($val));
                    }
                } catch (ReflectionException $e) {
                    // method does not exist
                }
            }

            return $record;
        } else {
            return Record::fromStdClass($data, $property->getName());
        }
    }

    public function visitDateTime($data, Property\DateTimeType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data instanceof \DateTime) {
        } elseif (is_string($data)) {
            $pattern = $property->getPattern();
            if (empty($pattern)) {
                $pattern = \DateTime::W3C;
            }

            $data = \DateTime::createFromFormat($pattern, $data);

            if (!$data) {
                throw new ValidationException($path . ' must be a valid date-time format (' . $pattern . ')');
            }
        } else {
            throw new ValidationException($path . ' must be a valid date-time format');
        }

        $this->assertValidatorConstraints($data, $path);

        return $this->createSimpleProperty($data, $property);
    }

    public function visitDate($data, Property\DateType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data instanceof \DateTime) {
            $data = Date::fromDateTime($data);
        } elseif (is_string($data)) {
            $result = preg_match('/^' . Date::getPattern() . '$/', $data);

            if ($result) {
                $data = new Date($data);
            } else {
                throw new ValidationException($path . ' must be a valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
        }

        $this->assertValidatorConstraints($data, $path);

        return $this->createSimpleProperty($data, $property);
    }

    public function visitDuration($data, Property\DurationType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data instanceof \DateInterval) {
            $data = Duration::fromDateInterval($data);
        } elseif (is_string($data)) {
            $result = preg_match('/^' . Duration::getPattern() . '$/', $data);

            if ($result) {
                $data = new Duration($data);
            } else {
                throw new ValidationException($path . ' must be a valid duration format [ISO8601]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid duration format [ISO8601]');
        }

        $this->assertValidatorConstraints($data, $path);

        return $this->createSimpleProperty($data, $property);
    }

    public function visitFloat($data, Property\FloatType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if (is_float($data)) {
        } elseif (is_int($data)) {
            $data = (float) $data;
        } elseif (is_string($data)) {
            $result = preg_match('/^(\+|-)?([0-9]+(\.[0-9]*)?|\.[0-9]+)([Ee](\+|-)?[0-9]+)?$/', $data);

            if ($result) {
                $data = (float) $data;
            } else {
                throw new ValidationException($path . ' must be a float format (i.e. 1.23, -1.23)');
            }
        } else {
            throw new ValidationException($path . ' must be a float format (i.e. 1.23, -1.23)');
        }

        $this->assertDecimalConstraints($data, $property, $path);

        $this->assertValidatorConstraints($data, $path);

        return $this->createSimpleProperty($data, $property);
    }

    public function visitInteger($data, Property\IntegerType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if (is_int($data)) {
        } elseif (is_string($data)) {
            $result = preg_match('/^[\-+]?[0-9]+$/', $data);

            if ($result) {
                $data = (int) $data;
            } else {
                throw new ValidationException($path . ' must be a integer format (i.e. 1, -2)');
            }
        } else {
            throw new ValidationException($path . ' must be a integer format (i.e. 1, -2)');
        }

        $this->assertDecimalConstraints($data, $property, $path);

        $this->assertValidatorConstraints($data, $path);

        $data = (int) $data;

        return $this->createSimpleProperty($data, $property);
    }

    public function visitString($data, Property\StringType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        // must be an string or an object which can be casted to an string
        if (is_string($data)) {
        } elseif (is_object($data) && method_exists($data, '__toString')) {
            $data = (string) $data;
        } else {
            throw new ValidationException($path . ' must be a string');
        }

        $this->assertPropertySimpleConstraints($data, $property, $path);

        $this->assertStringConstraints($data, $property, $path);

        $this->assertValidatorConstraints($data, $path);

        // data from a blob column gets returned as resource
        if (is_resource($data)) {
            $data = base64_encode(stream_get_contents($data, -1, 0));
        } else {
            $data = (string) $data;
        }

        return $this->createSimpleProperty($data, $property);
    }

    public function visitTime($data, Property\TimeType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data instanceof \DateTime) {
            $data = Time::fromDateTime($data);
        } elseif (is_string($data)) {
            $result = preg_match('/^' . Time::getPattern() . '$/', $data);

            if ($result) {
                $data = new Time($data);
            } else {
                throw new ValidationException($path . ' must be a valid full-time format (partial-time time-offset) [RFC3339]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid full-time format (partial-time time-offset) [RFC3339]');
        }

        $this->assertValidatorConstraints($data, $path);

        return $this->createSimpleProperty($data, $property);
    }

    protected function createSimpleProperty($data, PropertyInterface $property)
    {
        $reference = $property->getReference();
        if (!empty($reference)) {
            try {
                $class       = new ReflectionClass($reference);
                $constructor = $class->getConstructor();

                if ($constructor instanceof ReflectionMethod && $constructor->getNumberOfRequiredParameters() == 1) {
                    return $class->newInstance($data);
                }
            } catch (ReflectionException $e) {
                // class does not exist
            }
        }

        return $data;
    }

    protected function assertCompositeProperties(Property\CompositeTypeAbstract $property, $path)
    {
        if (count($property) == 0) {
            //throw new ValidationException($path . ' has no properties');
        }
    }

    protected function assertRequired($data, PropertyInterface $property, $path)
    {
        if ($property->isRequired() && $data === null) {
            throw new ValidationException($path . ' is required');
        }
    }

    protected function assertPropertySimpleConstraints($data, PropertySimpleAbstract $property, $path)
    {
        if ($data === null) {
            return;
        }

        if ($property->getPattern() !== null) {
            $result = preg_match('/^(' . $property->getPattern() . '){1}$/', $data);

            if (!$result) {
                throw new ValidationException($path . ' does not match pattern [' . $property->getPattern() . ']');
            }
        }

        if ($property->getEnumeration() !== null) {
            if (!in_array($data, $property->getEnumeration())) {
                throw new ValidationException($path . ' is not in enumeration [' . implode(', ', $property->getEnumeration()) . ']');
            }
        }
    }

    protected function assertDecimalConstraints($data, Property\DecimalType $property, $path)
    {
        if ($data === null) {
            return;
        }

        if ($property->getMax() !== null) {
            if ($data > $property->getMax()) {
                throw new ValidationException($path . ' must be lower or equal then ' . $property->getMax());
            }
        }

        if ($property->getMin() !== null) {
            if ($data < $property->getMin()) {
                throw new ValidationException($path . ' must be greater or equal then ' . $property->getMin());
            }
        }
    }

    protected function assertStringConstraints($data, Property\StringType $property, $path)
    {
        if ($data === null) {
            return;
        }

        if ($property->getMinLength() !== null) {
            if (strlen($data) < $property->getMinLength()) {
                throw new ValidationException($path . ' must contain more then ' . $property->getMinLength() . ' characters');
            }
        }

        if ($property->getMaxLength() !== null) {
            if (strlen($data) > $property->getMaxLength()) {
                throw new ValidationException($path . ' must contain less then ' . $property->getMaxLength() . ' characters');
            }
        }
    }

    protected function assertArrayConstraints(array $data, Property\ArrayType $property, $path)
    {
        if ($property->getMinLength() !== null) {
            if (count($data) < $property->getMinLength()) {
                throw new ValidationException($path . ' must contain more then ' . $property->getMinLength() . ' elements');
            }
        }

        if ($property->getMaxLength() !== null) {
            if (count($data) > $property->getMaxLength()) {
                throw new ValidationException($path . ' must contain less then ' . $property->getMaxLength() . ' elements');
            }
        }
    }

    protected function assertValidatorConstraints($data, $path)
    {
        if ($this->validator !== null) {
            $this->validator->validateProperty($path, $data);
        }
    }
}

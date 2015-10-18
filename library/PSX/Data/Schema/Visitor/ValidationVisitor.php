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

namespace PSX\Data\Schema\Visitor;

use PSX\Data\Record;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\PropertySimpleAbstract;
use PSX\Data\Schema\ValidationException;
use PSX\Data\Schema\VisitorInterface;
use PSX\DateTime;
use PSX\Validate\ValidatorInterface;
use RuntimeException;

/**
 * ValidationVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitor implements VisitorInterface
{
    protected $validator;

    /**
     * Sets an optional validator which can validate each value through custom
     * filters. This should only be used for filters which can not be defined 
     * inside a JsonSchema like i.e. check whether a row exists in a database
     */
    public function setValidator(ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
    }

    public function visitArray(array $data, Property\ArrayType $property, $path)
    {
        $this->assertCompositeProperties($property, $path);

        $this->assertArrayConstraints($data, $property, $path);

        $this->assertValidatorConstraints($data, $path);
    }

    public function visitBoolean($data, Property\BooleanType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif (is_bool($data)) {
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
    }

    public function visitComplex(\stdClass $data, Property\ComplexType $property, $path)
    {
        $this->assertCompositeProperties($property, $path);

        $this->assertValidatorConstraints($data, $path);
    }

    public function visitDateTime($data, Property\DateTimeType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif ($data instanceof \DateTime) {
        } elseif (is_string($data)) {
            $result = preg_match('/^' . DateTime::getPattern() . '$/', $data);

            if ($result) {
                $data = new DateTime($data);
            } else {
                throw new ValidationException($path . ' must be a valid date-time format (full-date "T" full-time) [RFC3339]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid date-time format (full-date "T" full-time) [RFC3339]');
        }

        $this->assertValidatorConstraints($data, $path);
    }

    public function visitDate($data, Property\DateType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif ($data instanceof \DateTime) {
        } elseif (is_string($data)) {
            $result = preg_match('/^' . DateTime\Date::getPattern() . '$/', $data);

            if ($result) {
                $data = new DateTime\Date($data);
            } else {
                throw new ValidationException($path . ' must be a valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
        }

        $this->assertValidatorConstraints($data, $path);
    }

    public function visitDuration($data, Property\DurationType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif ($data instanceof \DateInterval) {
        } elseif (is_string($data)) {
            $result = preg_match('/^' . DateTime\Duration::getPattern() . '$/', $data);

            if ($result) {
                $data = new DateTime\Duration($data);
            } else {
                throw new ValidationException($path . ' must be a valid duration format [ISO8601]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid duration format [ISO8601]');
        }

        $this->assertValidatorConstraints($data, $path);
    }

    public function visitFloat($data, Property\FloatType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif (is_float($data)) {
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
    }

    public function visitInteger($data, Property\IntegerType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif (is_int($data)) {
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
    }

    public function visitString($data, Property\StringType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        // must be an string or an object which can be casted to an string
        if ($data === null) {
        } elseif (is_string($data)) {
        } elseif (is_object($data) && method_exists($data, '__toString')) {
            $data = (string) $data;
        } else {
            throw new ValidationException($path . ' must be a string');
        }

        $this->assertPropertySimpleConstraints($data, $property, $path);

        $this->assertStringConstraints($data, $property, $path);

        $this->assertValidatorConstraints($data, $path);
    }

    public function visitTime($data, Property\TimeType $property, $path)
    {
        $this->assertRequired($data, $property, $path);

        if ($data === null) {
        } elseif ($data instanceof \DateTime) {
        } elseif (is_string($data)) {
            $result = preg_match('/^' . DateTime\Time::getPattern() . '$/', $data);

            if ($result) {
                $data = new DateTime\Time($data);
            } else {
                throw new ValidationException($path . ' must be a valid full-time format (partial-time time-offset) [RFC3339]');
            }
        } else {
            throw new ValidationException($path . ' must be a valid full-time format (partial-time time-offset) [RFC3339]');
        }

        $this->assertValidatorConstraints($data, $path);
    }

    protected function assertCompositeProperties(Property\CompositeTypeAbstract $property, $path)
    {
        if (count($property) == 0) {
            throw new RuntimeException($path . ' has no properties');
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

    protected function assertArrayConstraints($data, Property\ArrayType $property, $path)
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

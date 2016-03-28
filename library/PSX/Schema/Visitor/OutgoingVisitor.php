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
use PSX\Schema\RevealerInterface;
use PSX\Schema\ValidationException;
use PSX\DateTime\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Schema\VisitorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * OutgoingVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OutgoingVisitor implements VisitorInterface
{
    public function visitArray(array $data, Property\ArrayType $property, $path)
    {
        return $data;
    }

    public function visitBoolean($data, Property\BooleanType $property, $path)
    {
        return $data === 'false' ? false : (bool) $data;
    }

    public function visitComplex(\stdClass $data, Property\ComplexType $property, $path)
    {
        return Record::fromStdClass($data, $property->getName());
    }

    public function visitDateTime($data, Property\DateTimeType $property, $path)
    {
        $data = $this->fixDateTime($data);

        $pattern = $property->getPattern();
        if (empty($pattern)) {
            $pattern = \DateTime::W3C;
        }

        if (is_string($data)) {
            $data = DateTime::createFromFormat($pattern, $data);
            if (!$data) {
                throw new ValidationException($path . ' must be a valid date-time format (' . $pattern . ')');
            }
        } elseif ($data instanceof \DateTime) {
        } else {
            throw new ValidationException($path . ' must be a valid date-time format (' . $pattern . ')');
        }

        return str_replace('+00:00', 'Z', $data->format($pattern));
    }

    public function visitDate($data, Property\DateType $property, $path)
    {
        if (is_string($data)) {
            try {
                $data = new Date($data);
            } catch (\InvalidArgumentException $e) {
                throw new ValidationException($path . ' must be a valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
            }
        } elseif ($data instanceof \DateTime) {
            $data = Date::fromDateTime($data);
        } else {
            throw new ValidationException($path . ' must be a valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]');
        }

        return $data->toString();
    }

    public function visitDuration($data, Property\DurationType $property, $path)
    {
        if (is_string($data)) {
            try {
                $data = new Duration($data);
            } catch (\InvalidArgumentException $e) {
                throw new ValidationException($path . ' must be a valid duration format [ISO8601]');
            }
        } elseif ($data instanceof \DateInterval) {
            $data = Duration::fromDateInterval($data);
        } else {
            throw new ValidationException($path . ' must be a valid duration format [ISO8601]');
        }

        return $data->toString();
    }

    public function visitFloat($data, Property\FloatType $property, $path)
    {
        return (float) $data;
    }

    public function visitInteger($data, Property\IntegerType $property, $path)
    {
        return (int) $data;
    }

    public function visitString($data, Property\StringType $property, $path)
    {
        // data from a blob column gets returned as resource
        if (is_resource($data)) {
            $data = base64_encode(stream_get_contents($data, -1, 0));
        } else {
            $data = (string) $data;
        }

        return $data;
    }

    public function visitTime($data, Property\TimeType $property, $path)
    {
        if (is_string($data)) {
            try {
                $data = new Time($data);
            } catch (\InvalidArgumentException $e) {
                throw new ValidationException($path . ' must be a valid full-time format (partial-time time-offset) [RFC3339]');
            }
        } elseif ($data instanceof \DateTime) {
            $data = Time::fromDateTime($data);
        } else {
            throw new ValidationException($path . ' must be a valid full-time format (partial-time time-offset) [RFC3339]');
        }

        return $data->toString();
    }

    protected function fixDateTime($data)
    {
        if (is_string($data)) {
            // fix so that we understand mysql date time formats
            if (isset($data[10]) && $data[10] == ' ') {
                $data[10] = 'T';
            }

            // if we have no timezone we assume UTC
            if (strlen($data) == 19) {
                $data = $data . 'Z';
            }
        }

        return $data;
    }
}

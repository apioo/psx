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

namespace PSX\Swagger;

use InvalidArgumentException;
use PSX\Data\RecordAbstract;

/**
 * Property
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Property extends RecordAbstract
{
    const TYPE_INTEGER    = 'integer';
    const TYPE_NUMBER     = 'number';
    const TYPE_BOOLEAN    = 'boolean';
    const TYPE_STRING     = 'string';

    const FORMAT_INT32    = 'int32';
    const FORMAT_INT64    = 'int64';
    const FORMAT_FLOAT    = 'float';
    const FORMAT_DOUBLE   = 'double';
    const FORMAT_BYTE     = 'byte';
    const FORMAT_DATE     = 'date';
    const FORMAT_DATETIME = 'date-time';

    protected $id;
    protected $type;
    protected $format;
    protected $description;
    protected $defaultValue;
    protected $enum;
    protected $minimum;
    protected $maximum;
    protected $items;
    protected $uniqueItems;

    public function __construct($id = null, $type = null, $description = null)
    {
        $this->id          = $id;
        $this->description = $description;

        if ($type !== null) {
            $this->setType($type);
        }
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        if (!in_array($type, array(self::TYPE_INTEGER, self::TYPE_NUMBER, self::TYPE_STRING, self::TYPE_BOOLEAN))) {
            throw new InvalidArgumentException('Type must be one of integer, number, string, boolean');
        }

        $this->type = $type;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function setFormat($format)
    {
        if (!in_array($format, array(self::FORMAT_INT32, self::FORMAT_INT64, self::FORMAT_FLOAT, self::FORMAT_DOUBLE, self::FORMAT_BYTE, self::FORMAT_DATE, self::FORMAT_DATETIME))) {
            throw new InvalidArgumentException('Type must be one of int32, int64, float, double, byte, date, date-time');
        }

        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getDescription()
    {
        return $this->description;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }
    
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setEnum(array $enum)
    {
        $this->enum = $enum;
    }
    
    public function getEnum()
    {
        return $this->enum;
    }

    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }
    
    public function getMinimum()
    {
        return $this->minimum;
    }

    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }
    
    public function getMaximum()
    {
        return $this->maximum;
    }
}

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

namespace PSX\Model\Swagger;

/**
 * DataTypeTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait DataTypeTrait
{
    /**
     * @Type("string")
     */
    protected $type;

    /**
     * @Key("$ref")
     * @Type("string")
     */
    protected $ref;

    /**
     * @Type("string")
     */
    protected $format;

    /**
     * @Type("array<string>")
     */
    protected $enum;

    /**
     * @Type("integer")
     */
    protected $minimum;

    /**
     * @Type("integer")
     */
    protected $maximum;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getEnum()
    {
        return $this->enum;
    }

    public function setEnum($enum)
    {
        $this->enum = $enum;
    }

    public function getMinimum()
    {
        return $this->minimum;
    }

    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }

    public function getMaximum()
    {
        return $this->maximum;
    }

    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }
}

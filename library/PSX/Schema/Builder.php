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

namespace PSX\Schema;

/**
 * Builder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Builder
{
    protected $property;

    public function __construct($name)
    {
        $this->property = Property::getComplex($name);
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->property->setDescription($description);

        return $this;
    }

    /**
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->property->setRequired($required);

        return $this;
    }

    /**
     * @param string $reference
     * @return $this
     */
    public function setReference($reference)
    {
        $this->property->setReference($reference);

        return $this;
    }

    /**
     * @param \PSX\Schema\PropertyInterface $property
     * @return $this
     */
    public function add(PropertyInterface $property)
    {
        $this->property->add($property);

        return $this;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\AnyType
     */
    public function anyType($name)
    {
        if ($name instanceof Property\AnyType) {
            $this->add($property = $name);
        } else {
            $this->add($property = Property::getAny($name));
        }

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\ArrayType
     */
    public function arrayType($name)
    {
        if ($name instanceof Property\ArrayType) {
            $this->add($property = $name);
        } else {
            $this->add($property = Property::getArray($name));
        }

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\BooleanType
     */
    public function boolean($name)
    {
        $this->add($property = Property::getBoolean($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\ChoiceType
     */
    public function choiceType($name)
    {
        if ($name instanceof Property\ChoiceType) {
            $this->add($property = $name);
        } else {
            $this->add($property = Property::getChoice($name));
        }

        return $property;
    }

    /**
     * @param string $name
     * @param \PSX\Schema\Property\ComplexType $template
     * @return \PSX\Schema\Property\ComplexType
     */
    public function complexType($name, Property\ComplexType $template = null)
    {
        if ($template === null) {
            if ($name instanceof Property\ComplexType) {
                $this->add($property = $name);
            } else {
                $this->add($property = Property::getComplex($name));
            }
        } else {
            $property = clone $template;
            $property->setName($name);

            $this->add($property);
        }

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\DateType
     */
    public function date($name)
    {
        $this->add($property = Property::getDate($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\DateTimeType
     */
    public function dateTime($name)
    {
        $this->add($property = Property::getDateTime($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\DurationType
     */
    public function duration($name)
    {
        $this->add($property = Property::getDuration($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\FloatType
     */
    public function float($name)
    {
        $this->add($property = Property::getFloat($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\IntegerType
     */
    public function integer($name)
    {
        $this->add($property = Property::getInteger($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\StringType
     */
    public function string($name)
    {
        $this->add($property = Property::getString($name));

        return $property;
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\TimeType
     */
    public function time($name)
    {
        $this->add($property = Property::getTime($name));

        return $property;
    }

    /**
     * @return \PSX\Schema\Property\ComplexType
     */
    public function getProperty()
    {
        return $this->property;
    }
}

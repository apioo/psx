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
 * Factory class to access different property types. These methods should be
 * used instead of creating a new object
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class Property
{
    /**
     * @param string $name
     * @return \PSX\Schema\Property\AnyType
     */
    public static function getAny($name)
    {
        return new Property\AnyType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\ArrayType
     */
    public static function getArray($name)
    {
        return new Property\ArrayType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\BooleanType
     */
    public static function getBoolean($name)
    {
        return new Property\BooleanType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\ChoiceType
     */
    public static function getChoice($name)
    {
        return new Property\ChoiceType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\ComplexType
     */
    public static function getComplex($name)
    {
        return new Property\ComplexType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\DateTimeType
     */
    public static function getDateTime($name)
    {
        return new Property\DateTimeType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\DateType
     */
    public static function getDate($name)
    {
        return new Property\DateType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\DurationType
     */
    public static function getDuration($name)
    {
        return new Property\DurationType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\FloatType
     */
    public static function getFloat($name)
    {
        return new Property\FloatType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\IntegerType
     */
    public static function getInteger($name)
    {
        return new Property\IntegerType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\StringType
     */
    public static function getString($name)
    {
        return new Property\StringType($name);
    }

    /**
     * @param string $name
     * @return \PSX\Schema\Property\TimeType
     */
    public static function getTime($name)
    {
        return new Property\TimeType($name);
    }
}

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

namespace PSX\Data\Schema;

/**
 * Factory class to access different property types. These methods should be 
 * used instead of creating a new object since this gives PSX the possibility
 * to change the type implementation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class Property
{
	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\ArrayType
	 */
	public static function getArray($name)
	{
		return new Property\ArrayType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\BooleanType
	 */
	public static function getBoolean($name)
	{
		return new Property\BooleanType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\ComplexType
	 */
	public static function getComplex($name)
	{
		return new Property\ComplexType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\DateTimeType
	 */
	public static function getDateTime($name)
	{
		return new Property\DateTimeType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\DateType
	 */
	public static function getDate($name)
	{
		return new Property\DateType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\DurationType
	 */
	public static function getDuration($name)
	{
		return new Property\DurationType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\FloatType
	 */
	public static function getFloat($name)
	{
		return new Property\FloatType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\IntegerType
	 */
	public static function getInteger($name)
	{
		return new Property\IntegerType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\StringType
	 */
	public static function getString($name)
	{
		return new Property\StringType($name);
	}

	/**
	 * @param string $name
	 * @return PSX\Data\Schema\Property\TimeType
	 */
	public static function getTime($name)
	{
		return new Property\TimeType($name);
	}
}

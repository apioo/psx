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
 * VisitorInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface VisitorInterface
{
	/**
	 * Visits an array value
	 *
	 * @param array $data
	 * @param PSX\Data\Schema\Property\ArrayType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitArray(array $data, Property\ArrayType $property, $path);

	/**
	 * Visits an boolean value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\BooleanType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitBoolean($data, Property\BooleanType $property, $path);

	/**
	 * Visits an complex value
	 *
	 * @param stdClass $data
	 * @param PSX\Data\Schema\Property\ComplexType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitComplex(\stdClass $data, Property\ComplexType $property, $path);

	/**
	 * Visits an date time value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\DateTimeType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitDateTime($data, Property\DateTimeType $property, $path);

	/**
	 * Visits an date value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\DateType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitDate($data, Property\DateType $property, $path);

	/**
	 * Visits an duration value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\DurationType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitDuration($data, Property\DurationType $property, $path);

	/**
	 * Visits an float value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\FloatType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitFloat($data, Property\FloatType $property, $path);

	/**
	 * Visits an integer value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\IntegerType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitInteger($data, Property\IntegerType $property, $path);

	/**
	 * Visits an string value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\StringType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitString($data, Property\StringType $property, $path);

	/**
	 * Visits an time value
	 *
	 * @param string $data
	 * @param PSX\Data\Schema\Property\TimeType $property
	 * @param string $path
	 * @return mixed
	 */
	public function visitTime($data, Property\TimeType $property, $path);
}

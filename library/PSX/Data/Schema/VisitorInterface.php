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
	public function visitArray(array $data, Property\ArrayType $property, $path);

	public function visitBoolean($data, Property\BooleanType $property, $path);

	public function visitComplex(\stdClass $data, Property\ComplexType $property, $path);

	public function visitDateTime($data, Property\DateTimeType $property, $path);

	public function visitDate($data, Property\DateType $property, $path);

	public function visitDuration($data, Property\DurationType $property, $path);

	public function visitFloat($data, Property\FloatType $property, $path);

	public function visitInteger($data, Property\IntegerType $property, $path);

	public function visitString($data, Property\StringType $property, $path);

	public function visitTime($data, Property\TimeType $property, $path);
}

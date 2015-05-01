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

namespace PSX\Data\Schema\Property;

use PSX\Data\Schema\PropertySimpleAbstract;
use PSX\Data\Schema\ValidationException;

/**
 * DecimalType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class DecimalType extends PropertySimpleAbstract
{
	protected $max;
	protected $min;

	public function setMax($max)
	{
		$this->max = $max;

		return $this;
	}
	
	public function getMax()
	{
		return $this->max;
	}

	public function setMin($min)
	{
		$this->min = $min;

		return $this;
	}
	
	public function getMin()
	{
		return $this->min;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return md5(
			parent::getId() .
			$this->min .
			$this->max
		);
	}
}

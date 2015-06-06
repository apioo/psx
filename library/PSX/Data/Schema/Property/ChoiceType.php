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

use PSX\Data\Schema\PropertyAbstract;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\ValidationException;
use RuntimeException;

/**
 * ChoiceType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ChoiceType extends CompositeTypeAbstract
{
	public function validate($data, $path = '/')
	{
		parent::validate($data, $path);

		if($data === null)
		{
			return true;
		}

		foreach($this->properties as $property)
		{
			try
			{
				if($property->validate($data, $path) === true)
				{
					return true;
				}
			}
			catch(ValidationException $e)
			{
			}
		}

		throw new ValidationException($path . ' must be one of the following objects [' . implode(', ', array_keys($this->properties)) . ']');
	}

	public function assimilate($data, $path = '/')
	{
		parent::assimilate($data, $path);

		$matches = array();
		foreach($this->properties as $index => $property)
		{
			$value = $property->match($data);
			if($value > 0)
			{
				$matches[$index] = $value;
			}
		}

		if(empty($matches))
		{
			throw new RuntimeException($path . ' must be one of the following objects [' . implode(', ', array_keys($this->properties)) . ']');
		}

		arsort($matches);

		return $this->properties[key($matches)]->assimilate($data);
	}
}

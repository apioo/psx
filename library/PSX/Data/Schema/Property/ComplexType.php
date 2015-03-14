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

/**
 * ComplexType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ComplexType extends PropertyAbstract
{
	protected $properties = array();

	public function add(PropertyInterface $property)
	{
		$this->properties[$property->getName()] = $property;

		return $this;
	}

	public function get($name)
	{
		return isset($this->properties[$name]) ? $this->properties[$name] : null;
	}

	public function has($name)
	{
		return isset($this->properties[$name]);
	}

	public function remove($name)
	{
		if(isset($this->properties[$name]))
		{
			unset($this->properties[$name]);
		}
	}

	public function getProperties()
	{
		return $this->properties;
	}

	public function getId()
	{
		$result     = parent::getId();
		$properties = $this->properties;

		ksort($properties);

		foreach($properties as $name => $property)
		{
			$result.= $name . $property->getId();
		}

		return md5($result);
	}

	public function validate($data)
	{
		parent::validate($data);

		if($data === null)
		{
			return true;
		}

		if(!$data instanceof \stdClass)
		{
			throw new ValidationException($this->getName() . ' must be an object');
		}

		return true;
	}
}

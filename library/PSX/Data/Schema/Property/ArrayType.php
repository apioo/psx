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
use Traversable;

/**
 * ArrayType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ArrayType extends CompositeTypeAbstract
{
	protected $minLength;
	protected $maxLength;

	public function add(PropertyInterface $property)
	{
		return $this->setPrototype($property);
	}

	public function setPrototype(PropertyInterface $prototype)
	{
		$this->properties = array('prototype' => $prototype);

		return $this;
	}

	public function getPrototype()
	{
		return current($this->properties);
	}

	public function setMinLength($minLength)
	{
		$this->minLength = $minLength;

		return $this;
	}

	public function getMinLength()
	{
		return $this->minLength;
	}

	public function setMaxLength($maxLength)
	{
		$this->maxLength = $maxLength;

		return $this;
	}

	public function getMaxLength()
	{
		return $this->maxLength;
	}

	public function validate($data, $path = '/')
	{
		parent::validate($data, $path);

		if($data === null)
		{
			return true;
		}

		if(!is_array($data))
		{
			throw new ValidationException($path . ' must be an array');
		}

		if($this->minLength !== null)
		{
			if(count($data) < $this->minLength)
			{
				throw new ValidationException($path . ' must contain more then ' . $this->minLength . ' elements');
			}
		}

		if($this->maxLength !== null)
		{
			if(count($data) > $this->maxLength)
			{
				throw new ValidationException($path . ' must contain less then ' . $this->maxLength . ' elements');
			}
		}

		$prototype = $this->getPrototype();
		foreach($data as $key => $value)
		{
			$prototype->validate($value, $path . '/' . $key);
		}

		return true;
	}

	public function assimilate($data, $path = '/')
	{
		parent::assimilate($data, $path);

		if(!is_array($data) && !$data instanceof Traversable)
		{
			throw new RuntimeException($path . ' must be an array');
		}

		$prototype = $this->getPrototype();
		$result    = array();

		foreach($data as $value)
		{
			$result[] = $prototype->assimilate($value);
		}

		return $result;
	}
}

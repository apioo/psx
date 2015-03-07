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

namespace PSX\Validate;

use InvalidArgumentException;
use PSX\DisplayException;

/**
 * ArrayValidator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ArrayValidator extends ValidatorAbstract
{
	public function validate($data)
	{
		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$cleanData = array();

		foreach($data as $key => $value)
		{
			$cleanData[$key] = $this->getPropertyValue($this->getProperty($key), $value, $key);
		}

		// we fill up missing property names with null values
		$names     = $this->getPropertyNames();
		$diffNames = array_diff($names, array_keys($cleanData));

		if(!empty($diffNames))
		{
			foreach($diffNames as $name)
			{
				$cleanData[$name] = null;
			}
		}

		return $cleanData;
	}
}

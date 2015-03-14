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

namespace PSX\Data\Record;

use PSX\Data\RecordInterface;

/**
 * GraphTraverser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GraphTraverser
{
	public function traverse($record, VisitorInterface $visitor)
	{
		$this->traverseObject($record, $visitor);
	}

	protected function traverseObject($object, VisitorInterface $visitor)
	{
		$name = null;

		if($object instanceof RecordInterface)
		{
			$properties = $object->getRecordInfo();
			$name       = $properties->getName();
		}
		else if($object instanceof \stdClass)
		{
			$properties = (array) $object;
		}

		if(empty($name))
		{
			$name = 'record';
		}

		$visitor->visitObjectStart($name);

		foreach($properties as $key => $value)
		{
			$visitor->visitObjectValueStart($key, $value);

			$this->traverseValue($value, $visitor);

			$visitor->visitObjectValueEnd();
		}

		$visitor->visitObjectEnd();
	}

	protected function traverseValue($value, VisitorInterface $visitor)
	{
		if(self::isArray($value))
		{
			$visitor->visitArrayStart($value);

			foreach($value as $val)
			{
				$visitor->visitArrayValueStart($val);

				$this->traverseValue($val, $visitor);

				$visitor->visitArrayValueEnd();
			}

			$visitor->visitArrayEnd();
		}
		else if(self::isObject($value))
		{
			$this->traverseObject($value, $visitor);
		}
		else
		{
			$visitor->visitValue($value);
		}
	}

	public static function isObject($value)
	{
		return $value instanceof RecordInterface || $value instanceof \stdClass || $value instanceof JsonSerializable;
	}

	public static function isArray($value)
	{
		return is_array($value);
	}
}

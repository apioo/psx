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

namespace PSX\Template;

use PSX\Data\RecordInterface;

/**
 * GeneratorAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GeneratorAbstract implements GeneratorInterface
{
	protected function getData(RecordInterface $record)
	{
		$recordInfo = $record->getRecordInfo();
		$data       = array();

		foreach($recordInfo as $key => $value)
		{
			$data[$key] = $this->getValue($value);
		}

		return $data[$key];
	}

	protected function getValue($field)
	{
		if(is_array($field))
		{
			$data = array();
			foreach($field as $value)
			{
				$data[] = $this->getValue($value);
			}
			return $data;
		}
		else if($field instanceof RecordInterface)
		{
			return $this->getData($v);
		}
		else if($field instanceof \DateTime)
		{
			return $v->format(\DateTime::RFC3339);
		}
		else if(is_object($field))
		{
			return (string) $v;
		}
		else if(is_bool($field))
		{
			return $field ? '1' : '0';
		}
		else
		{
			return $field;
		}
	}
}

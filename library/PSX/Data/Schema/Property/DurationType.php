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

use PSX\Data\Schema\ValidationException;
use PSX\DateTime\Duration;
use RuntimeException;

/**
 * DurationType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DurationType extends StringType
{
	public function validate($data, $path = '/')
	{
		if($data instanceof \DateInterval)
		{
			return true;
		}

		parent::validate($data, $path);

		if($data === null)
		{
			return true;
		}
		else if(is_string($data))
		{
			$result = preg_match('/^' . \PSX\DateTime\Duration::getPattern() . '$/', $data);

			if($result)
			{
				return true;
			}
		}

		throw new ValidationException($path . ' must be an valid duration format [ISO8601]');
	}

	public function assimilate($data, $path = '/')
	{
		if($data instanceof \DateInterval)
		{
			return Duration::fromDateInterval($data);
		}

		parent::assimilate($data, $path);

		try
		{
			return new Duration($data);
		}
		catch(\Exception $e)
		{
			throw new RuntimeException($path . ' must be an valid duration format [ISO8601]');
		}
	}
}

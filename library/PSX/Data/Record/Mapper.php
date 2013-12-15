<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Data\Record;

use InvalidArgumentException;
use PSX\Data\RecordInterface;
use PSX\Data\Record\Mapper\Rule;

/**
 * Class wich can map all fields of an record to an arbitary class by calling
 * the fitting setter methods if available
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Mapper
{
	protected $rule;

	public function setRule(array $rule)
	{
		$this->rule = $rule;
	}

	public function map(RecordInterface $source, $destination)
	{
		if(!is_object($destination))
		{
			throw new InvalidArgumentException('Destination must be an object');
		}

		$data = $source->getRecordInfo()->getData();

		foreach($data as $key => $value)
		{
			// convert to camelcase if underscore is in name
			if(strpos($key, '_') !== false)
			{
				$key = implode('', array_map('ucfirst', explode('_', $key)));
			}

			if(isset($this->rule[$key]))
			{
				if(is_string($this->rule[$key]))
				{
					$method = 'set' . ucfirst($this->rule[$key]);
				}
				else if($this->rule[$key] instanceof Rule)
				{
					$method = 'set' . ucfirst($this->rule[$key]->getName());
					$value  = $this->rule[$key]->getValue($value);
				}
			}
			else
			{
				$method = 'set' . ucfirst($key);
			}

			if(is_callable(array($destination, $method)))
			{
				$destination->$method($value);
			}
		}
	}
}


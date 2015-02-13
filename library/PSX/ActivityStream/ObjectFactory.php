<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\ActivityStream;

use PSX\Data\Record\FactoryInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Util\CurveArray;

/**
 * Object
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ObjectFactory implements FactoryInterface
{
	public function factory($data, ImporterInterface $importer)
	{
		if(is_array($data))
		{
			if(!CurveArray::isAssoc($data))
			{
				$objects = array();

				foreach($data as $row)
				{
					$objects[] = $this->factory($row, $importer);
				}

				return $objects;
			}
			else
			{
				$class = null;

				if(isset($data['objectType']) && !empty($data['objectType']))
				{
					$class = $this->resolveType($data['objectType']);
				}

				if($class !== null && class_exists($class))
				{
					$object = new $class();
				}
				else
				{
					$object = new Object();
				}

				return $importer->import($object, $data);
			}
		}
		else
		{
			$object = new Object();
			$object->setUrl((string) $data);

			return $data;
		}
	}

	protected function resolveType($type)
	{
		if(is_array($type))
		{
			$type = isset($type['id']) ? $type['id'] : null;
		}
		else
		{
			$type = (string) $type;
		}

		$type  = strtolower($type);
		$class = null;

		switch($type)
		{
			case 'activity':
			case 'audio':
			case 'binary':
			case 'collection':
			case 'event':
			case 'group':
			case 'issue':
			case 'permission':
			case 'place':
			case 'role':
			case 'task':
			case 'video':
				$class = 'PSX\\ActivityStream\\ObjectType\\' . ucfirst($type);
				break;
		}

		return $class;
	}
}

<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Data\BuilderInterface;
use PSX\Data\Record\DefaultImporter;

/**
 * LinkBuilder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LinkBuilder implements BuilderInterface
{
	public function build($data)
	{
		if(is_array($data))
		{
			if(isset($data[0]))
			{
				$objects = array();

				foreach($data as $row)
				{
					$objects[] = $this->build($row);
				}

				return $objects;
			}
			else
			{
				$class = null;

				if(isset($data['objectType']) && !empty($data['objectType']))
				{
					if(is_array($data['objectType']))
					{
						$objectType = isset($data['objectType']['id']) ? $data['objectType']['id'] : null;
					}
					else
					{
						$objectType = (string) $data['objectType'];
					}

					$class = 'PSX\ActivityStream\ObjectType\\' . ucfirst(strtolower($objectType));
				}

				if($class !== null && class_exists($class))
				{
					$object = new $class();
				}
				else
				{
					$object = isset($data['url']) ? new LinkObject() : new Object();
				}

				$importer = new DefaultImporter();
				$importer->import($object, $data);

				return $object;
			}
		}
		else
		{
			return $data;
		}
	}
}

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

namespace PSX\Loader\RoutingParser;

use PSX\Loader\RoutingCollection;
use PSX\Loader\RoutingParserInterface;
use PSX\Util\AnnotationParser;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;

/**
 * A simple routing parser which gets all informations from an key value array
 * which is useful for testing
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ArrayCollection implements RoutingParserInterface
{
	protected $routings;

	public function __construct(array $routings)
	{
		$this->routings = $routings;
	}

	public function getCollection()
	{
		$collection = new RoutingCollection();

		foreach($this->routings as $path => $source)
		{
			$collection->add(array('GET', 'POST', 'PUT', 'DELETE'), $path, $source);
		}

		return $collection;
	}
}

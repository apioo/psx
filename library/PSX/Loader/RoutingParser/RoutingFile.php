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

namespace PSX\Loader\RoutingParser;

use PSX\Loader\RoutingCollection;
use PSX\Loader\RoutingParserInterface;

/**
 * RoutingFile
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RoutingFile implements RoutingParserInterface
{
	protected $file;

	protected $_collection;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function getCollection()
	{
		if($this->_collection === null)
		{
			$collection = new RoutingCollection();
			$lines      = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			foreach($lines as $line)
			{
				$line = trim(str_replace("\t", ' ', $line));

				if(!empty($line) && $line[0] != '#')
				{
					$line    = preg_replace('/([\s]{1,})/', ' ', $line);
					$parts   = explode(' ', $line);
					$allowed = isset($parts[0]) ? explode('|', $parts[0]) : array();
					$path    = isset($parts[1]) ? $parts[1] : null;
					$class   = isset($parts[2]) ? $parts[2] : null;

					if(!empty($allowed) && !empty($path) && !empty($class))
					{
						$collection->add($allowed, $path, $class);
					}
				}
			}

			$this->_collection = $collection;
		}

		return $this->_collection;
	}
}

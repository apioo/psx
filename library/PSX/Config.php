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

namespace PSX;

use ArrayIterator;
use PSX\Config\NotFoundException;

/**
 * The class is the common config in psx. It includes the file provided in the
 * first argument of the constructor. This file must return an array. Here an 
 * example how to create an access the config.
 * <code>
 * $config = new Config('configuration.php');
 *
 * echo $config['psx_url'];
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Config extends ArrayIterator
{
	/**
	 * The container for the config array
	 *
	 * @var array
	 */
	private $container = array();

	public function __construct($file)
	{
		$config = null;

		if(is_array($file))
		{
			$config = $file;
		}
		else
		{
			$return = include($file);

			if(is_array($return))
			{
				$config = $return;
			}
		}

		if(is_array($config))
		{
			// assign container
			parent::__construct($config);
		}
		else
		{
			throw new NotFoundException('Couldnt find config in file');
		}
	}

	public function set($key, $value)
	{
		$this->offsetSet($key, $value);
	}

	public function get($key)
	{
		return $this->offsetGet($key);
	}

	public function has($key)
	{
		return $this->offsetExists($key);
	}
}


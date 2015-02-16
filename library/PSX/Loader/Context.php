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

namespace PSX\Loader;

/**
 * Contains context values which are gathered around an controller
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Context
{
	/**
	 * This key holds the route which was used to resolve the controller
	 */
	const KEY_PATH = 'psx.path';

	/**
	 * This key holds the variable fragment values from the uri path. I.e. if
	 * we have an path /foo/:bar the array would look like ['bar' => 'test']
	 * where test is the value from the actual request uri
	 */
	const KEY_FRAGMENT = 'psx.fragment';

	/**
	 * This key contains the raw source like defined in the routing file i.e. 
	 * Foo\Bar::doIndex
	 */
	const KEY_SOURCE = 'psx.source';

	/**
	 * This key contains the class name from the source
	 */
	const KEY_CLASS = 'psx.class';

	/**
	 * This key contains the method name from the source
	 */
	const KEY_METHOD = 'psx.method';

	/**
	 * This key contains an Exception if the error controller gets invoked
	 */
	const KEY_EXCEPTION = 'psx.exception';

	/**
	 * @var array
	 */
	protected $attributes = array();

	public function set($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	public function has($key)
	{
		return isset($this->attributes[$key]);
	}

	public function get($key)
	{
		return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
	}
}

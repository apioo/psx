<?php
/*
 *  $Id: Registry.php 616 2012-08-25 11:16:03Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

use ArrayObject;

/**
 * This class is for managing global objects in your application. Here some
 * examples howto set and get objects:
 * <code>
 * // set an object
 * PSX_Registry::set('stdClass', new stdClass);
 *
 * PSX_Registry::getInstance()->offsetSet('stdClass', new stdClass);
 *
 * PSX_Registry::getInstance()->stdClass = new stdClass();
 *
 *
 * // get an object
 * $stdClass = PSX_Registry::get('stdClass');
 *
 * $stdClass = PSX_Registry::getInstance()->offsetGet('stdClass');
 *
 * $stdClass = PSX_Registry::getInstance()->stdClass;
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Registry
 * @version    $Revision: 616 $
 */
class Registry extends ArrayObject
{
	protected static $_instance;

	protected $container = array();

	public function __construct()
	{
		parent::__construct($this->container, parent::ARRAY_AS_PROPS);
	}

	public function clear()
	{
		$this->exchangeArray($this->container = array());
	}

	public static function getInstance()
	{
		if(self::$_instance === null)
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function get($key)
	{
		return self::getInstance()->offsetGet($key);
	}

	public static function set($key, $value)
	{
		self::getInstance()->offsetSet($key, $value);
	}

	public static function has($key)
	{
		return self::getInstance()->offsetExists($key);
	}
}


<?php
/*
 *  $Id: Exception.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Loader;

use ReflectionClass;

/**
 * Represents an location wich must be returned by an LocationFinderInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Loader
 * @version    $Revision: 480 $
 */
class Location
{
	private $id;
	private $path;
	private $class;

	public function __construct($id, $path, ReflectionClass $class)
	{
		$this->id    = $id;
		$this->path  = $path;
		$this->class = $class;
	}

	/**
	 * Returns an unique id representing this location
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns the path. The loader will try to find the method wich should be
	 * called depending on this path and the @path parameter in the docblock
	 * comments of the module
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns the class of the module. The loader creates a new instance of it
	 *
	 * @return ReflectionClass
	 */
	public function getClass()
	{
		return $this->class;
	}
}

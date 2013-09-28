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

namespace PSX;

/**
 * Interface wich describes an template engine. If your code uses these methods 
 * you can simply switch between template engines without changing the buisness 
 * logic
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface TemplateInterface
{
	/**
	 * Sets the dir from where to load the template file
	 *
	 * @param string $dir
	 * @return void
	 */
	public function setDir($dir);

	/**
	 * Sets the current template file
	 *
	 * @param string $file
	 * @return void
	 */
	public function set($file);

	/**
	 * Returns the template file wich was set
	 *
	 * @return string
	 */
	public function get();

	/**
	 * Returns whether an template file was set or not
	 *
	 * @return boolean
	 */
	public function hasFile();

	/**
	 * Assigns an variable to the template
	 *
	 * @param string $name
	 * @return void
	 */
	public function assign($key, $value);

	/**
	 * Transforms the template file
	 *
	 * @return string
	 */
	public function transform();
}

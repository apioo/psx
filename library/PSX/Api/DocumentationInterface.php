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

namespace PSX\Api;

use PSX\Data\SchemaInterface;

/**
 * DocumentationInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface DocumentationInterface
{
	/**
	 * Returns whether an view exists for the given version
	 *
	 * @return boolean
	 */
	public function hasView($version);

	/**
	 * Returns the view for the given version
	 *
	 * @return PSX\Api\View
	 */
	public function getView($version);

	/**
	 * Returns an array containing all registered views on this documentation
	 *
	 * @return array
	 */
	public function getViews();

	/**
	 * Returns the latest version number
	 *
	 * @return integer
	 */
	public function getLatestVersion();

	/**
	 * Returns whether the API requires an version information in the content 
	 * type header
	 *
	 * @return boolean
	 */
	public function isVersionRequired();

	/**
	 * Returns an description about the API endpoint. The content may contain 
	 * html
	 *
	 * @return string
	 */
	public function getDescription();
}

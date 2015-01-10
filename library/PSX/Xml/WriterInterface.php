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

namespace PSX\Xml;

/**
 * Contract to all xml producing writer classes. The classes are designed to 
 * used either independently producing an complete xml document or used in an
 * context producing only an xml fragment
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface WriterInterface
{
	/**
	 * Closes all open element levels
	 *
	 * @return void
	 */
	public function close();

	/**
	 * Returns the xml as string. In most cases this method calls the close()
	 * method, ends the document and returns the result as string
	 *
	 * @return string
	 */
	public function toString();

	/**
	 * Returns the underlying xml writer
	 *
	 * @return XMLWriter
	 */
	public function getWriter();
}

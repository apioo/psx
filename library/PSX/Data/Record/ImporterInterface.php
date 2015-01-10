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

namespace PSX\Data\Record;

/**
 * The importer takes meta informations from an source and returns an record 
 * containing the actual data
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface ImporterInterface
{
	const ENTITY = 'PSX\Data\Record\Importer\Entity';
	const RECORD = 'PSX\Data\Record\Importer\Record';
	const SCHEMA = 'PSX\Data\Record\Importer\Schema';
	const TABLE  = 'PSX\Data\Record\Importer\Table';

	/**
	 * Returns whether the source is acceptable for this importer
	 *
	 * @param mixed $source
	 * @return boolean
	 */
	public function accept($source);

	/**
	 * @param mixed $source
	 * @param mixed $data
	 * @return PSX\Data\RecordInterface
	 */
	public function import($source, $data);
}

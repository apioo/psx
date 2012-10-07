<?php
/*
 *  $Id: RecordInterface.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_Data_RecordInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
interface PSX_Data_RecordInterface
{
	/**
	 * Returns the name of the record or null
	 *
	 * @return string|null
	 */
	public function getName();

	/**
	 * Returns an array representation of all record fields
	 *
	 * @return array
	 */
	public function getFields();

	/**
	 * This methods accept any number of parameters and checks whether each
	 * field exists in the current record
	 *
	 * @param string $field_1
	 * @param string ...
	 * @param string $field_n
	 * @return boolean
	 */
	public function hasFields();

	/**
	 * Returns all record fields wich are not null
	 *
	 * @return array
	 */
	public function getData();

	/**
	 * This method imports the data of an ReaderResult objekt into this object
	 *
	 * @return void
	 */
	public function import(PSX_Data_ReaderResult $result);

	/**
	 * This method returns arbitiary data wich is proccesed by an
	 * WriterInterface
	 *
	 * @return mixed
	 */
	public function export(PSX_Data_WriterResult $result);
}


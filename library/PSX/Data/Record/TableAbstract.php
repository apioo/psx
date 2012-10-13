<?php
/*
 *  $Id: TableAbstract.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Data_Record_TableAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
abstract class PSX_Data_Record_TableAbstract extends PSX_Data_RecordAbstract
{
	protected $_table;

	public function __construct(PSX_Sql_TableInterface $table)
	{
		$this->_table = $table;
	}

	public function getName()
	{
		return $this->_table->getDisplayName();
	}

	public function getFields()
	{
		$fields  = array();
		$columns = $this->_table->getColumns();

		foreach($columns as $column => $attr)
		{
			$fields[$column] = isset($this->$column) ? $this->$column : null;
		}

		return $fields;
	}
}

<?php
/*
 *  $Id: Util.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Sql_Util
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 480 $
 */
class PSX_Sql_Util
{
	/**
	 * The method parses an SQL statment and tries to extract the selected
	 * fields, table and the condition (WHERE, JOIN etc.) of the query. So
	 * you can only parse SELECTS with this method. If the parsing fails the
	 * method throws an exception. It returns an array where the first
	 * value is an array wich contains the selected fields the second is the
	 * selected table as string and the third is the condition.
	 *
	 * @param string $stmt
	 * @return array
	 */
	public static function parse($stmt)
	{
		// we try to get fast the fields and table from the query
		// so we must split up the query in three parts the fields
		// the table and anything behind the table (wich is maybe
		// a condition or a join)
		$s = strpos($stmt, 'SELECT');
		$f = strpos($stmt, 'FROM');

		if($s !== false && $f !== false)
		{
			// fields
			$fields     = array();
			$raw_fields = substr($stmt, $s + 6, $f - 6);
			$raw_fields = explode(',', $raw_fields);

			foreach($raw_fields as $field)
			{
				$as    = strpos($field, 'AS');
				$field = $as !== false ? trim(substr($field, 0, $as)) : trim($field);

				$fields[] = $field;
			}

			// remove the fields from the stmt
			$stmt = substr($stmt, $f + 4);


			// table
			$table     = '';
			$raw_table = false;
			$keywods   = array('INNER', 'WHERE');

			foreach($keywods as $key)
			{
				$p = strpos($stmt, $key);

				if($p !== false)
				{
					$raw_table = substr($stmt, 0, $p);

					break;
				}
			}

			if($raw_table === false)
			{
				$raw_table = $stmt;
			}

			$table = trim($raw_table);

			// remove the table from the stmt
			$stmt = substr($stmt, strlen($raw_table));


			// condition
			$condition = PSX_Sql_Condition::parse(trim($stmt));


			return array($fields, $table, $condition);
		}
		else
		{
			throw new PSX_Sql_Exception('Invalid sql statment');
		}
	}
}


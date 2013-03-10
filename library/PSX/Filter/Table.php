<?php
/*
 *  $Id: Table.php 409 2012-02-25 14:10:51Z k42b3.x@googlemail.com $
 *
 * amun
 * A social content managment system based on the psx framework. For
 * the current version and informations visit <http://amun.phpsx.org>
 *
 * Copyright (c) 2010 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of amun. amun is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * amun is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with amun. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Filter;

use PSX\FilterAbstract;
use PSX\Sql;

/**
 * PSX_Filter_Table
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   PSX
 * @package    PSX_Filter
 * @version    $Revision: 409 $
 */
class Table extends FilterAbstract
{
	private $sql;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;
	}

	public function apply($value)
	{
		$result = $this->sql->getAll('SHOW TABLES');
		$tables = array();

		foreach($result as $row)
		{
			$tables[] = current($row);
		}

		return in_array($value, $tables);
	}

	public function getErrorMsg()
	{
		return '%s is not valid table';
	}
}


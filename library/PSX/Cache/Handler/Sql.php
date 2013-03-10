<?php
/*
 *  $Id: Sql.php 636 2012-09-01 10:32:42Z k42b3.x@googlemail.com $
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

namespace PSX\Cache\Handler;

use PSX\DateTime;
use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;
use PSX\Sql\TableInterface;
use PSX\Sql\Condition;
use UnexpectedValueException;

/**
 * This handler stores cache entries in an simple sql table. The table must have
 * the following structure
 * <code>
 * CREATE TABLE IF NOT EXISTS `psx_cache` (
 *   `id` varchar(32) NOT NULL,
 *   `content` blob NOT NULL,
 *   `date` datetime NOT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Cache
 * @version    $Revision: 636 $
 */
class Sql implements HandlerInterface
{
	private $table;

	public function __construct(TableInterface $table)
	{
		$this->table = $table;
	}

	public function load($key)
	{
		$con = new Condition(array($this->table->getPrimaryKey(), '=', $key));
		$row = $this->table->getRow(array('content', 'date'), $con);

		if(!empty($row))
		{
			$content = $row['content'];
			$time    = strtotime($row['date']);

			return new Item($content, $time);
		}
		else
		{
			return false;
		}
	}

	public function write($key, $content, $expire)
	{
		$columnId      = $this->table->getPrimaryKey();
		$columnContent = $this->table->getFirstColumnWithType(TableInterface::TYPE_BLOB);
		$columnDate    = $this->table->getFirstColumnWithType(TableInterface::TYPE_DATETIME);

		if(empty($columnId))
		{
			throw new UnexpectedValueException('Missing column "id" in table ' . $this->table->getName());
		}

		if(empty($columnContent))
		{
			throw new UnexpectedValueException('Missing column "content" in table ' . $this->table->getName());
		}

		if(empty($columnDate))
		{
			throw new UnexpectedValueException('Missing column "date" in table ' . $this->table->getName());
		}

		$this->table->insert(array(

			$columnId      => $key,
			$columnContent => $content,
			$columnDate    => date(DateTime::SQL),

		));
	}

	public function remove($key)
	{
		$con = new Condition(array($this->table->getPrimaryKey(), '=', $key));

		$this->table->delete($con);
	}

	public function getTable()
	{
		return $this->table;
	}
}


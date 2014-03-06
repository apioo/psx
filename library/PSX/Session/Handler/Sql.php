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

namespace PSX\Session\Handler;

use PSX\DateTime;
use PSX\Sql\Condition;
use PSX\Sql\TableInterface;
use PSX\Sql\Table\ColumnAllocation;
use SessionHandlerInterface;

/**
 * Sql
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sql implements SessionHandlerInterface
{
	const COLUMN_ID      = 0x1;
	const COLUMN_CONTENT = 0x2;
	const COLUMN_DATE    = 0x3;

	protected $table;
	protected $allocation;

	public function __construct(TableInterface $table, ColumnAllocation $allocation)
	{
		$this->table      = $table;
		$this->allocation = $allocation;
	}

	public function open($path, $name)
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($id)
	{
		return $this->table->getField('content', new Condition(array($this->allocation->get(self::COLUMN_ID), '=', $id)));
	}

	public function write($id, $data)
	{
		$columnId      = $this->allocation->get(self::COLUMN_ID);
		$columnContent = $this->allocation->get(self::COLUMN_CONTENT);
		$columnDate    = $this->allocation->get(self::COLUMN_DATE);

		$this->table->insert(array(
			$columnId      => $id,
			$columnContent => $data,
			$columnDate    => date(DateTime::SQL),
		));
	}

	public function destroy($id)
	{
		$this->table->delete(new Condition(array($this->allocation->get(self::COLUMN_ID), '=', $id)));
	}

	public function gc($maxTime)
	{
		$con = new Condition();
		$con->add('DATE_ADD(`date`, "INTERVAL ' . $maxTime . ' SECOND")', '<', date(DateTime::SQL));

		$this->table->delete($con);

		return true;
	}
}

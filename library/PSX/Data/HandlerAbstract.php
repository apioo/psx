<?php
/*
 *  $Id: HandlerAbstract.php 644 2012-09-30 22:49:59Z k42b3.x@googlemail.com $
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
 * PSX_Data_HandlerAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 644 $
 */
abstract class PSX_Data_HandlerAbstract implements PSX_Data_HandlerInterface
{
	protected $table;

	public function __construct(PSX_Sql_TableInterface $table)
	{
		$this->table = $table;
	}

	public function create(PSX_Data_RecordInterface $record)
	{
		$this->table->insert($record);
	}

	public function update(PSX_Data_RecordInterface $record)
	{
		$this->table->update($record);
	}

	public function delete(PSX_Data_RecordInterface $record)
	{
		$this->table->delete($record);
	}
}

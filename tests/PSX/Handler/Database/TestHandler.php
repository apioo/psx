<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Handler\Database;

use PSX\Handler\DatabaseHandlerAbstract;
use PSX\Sql\Table;
use PSX\Sql\TableAbstract;
use PSX\Sql\TableInterface;

/**
 * TestHandler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestHandler extends DatabaseHandlerAbstract
{
	public function getDefaultSelect()
	{
		return $this->manager->getTable('PSX\Handler\Database\TestTable')
			->select(array('id', 'userId', 'title', 'date'));
	}
}

class TestTable extends TableAbstract
{
	public function getName()
	{
		return 'psx_handler_comment';
	}

	public function getColumns()
	{
		return array(
			'id'     => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
			'userId' => TableInterface::TYPE_INT | 10,
			'title'  => TableInterface::TYPE_VARCHAR | 32,
			'date'   => TableInterface::TYPE_DATETIME,
		);
	}
}

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

namespace PSX\Filter;

use PSX\Sql\DbTestCase;
use PSX\Sql\Table;
use PSX\Sql\TableInterface;

/**
 * PrimaryKeyTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PrimaryKeyTest extends DbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(__DIR__ . '/../Sql/table_fixture.xml');
	}

	public function testFilter()
	{
		$table  = getContainer()->get('table_manager')->getTable('PSX\Sql\TestTable');
		$filter = new PrimaryKey($table);

		$this->assertEquals(true, $filter->apply(1));
		$this->assertEquals(false, $filter->apply(32));

		// test error message
		$this->assertEquals('%s does not exist in table', $filter->getErrorMessage());
	}

	public function testFilterTableWithoutPk()
	{
		$table  = new Table($this->connection, 'psx_handler_comment', array('name' => TableInterface::TYPE_VARCHAR));
		$filter = new PrimaryKey($table);

		$this->assertEquals(false, $filter->apply(1));
		$this->assertEquals(false, $filter->apply(32));
	}
}

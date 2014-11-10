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

namespace PSX\Sql\Table\Reader;

use PSX\DateTime;
use PSX\Sql\DoctrineTestCase;
use PSX\Sql\Table;
use PSX\Sql\TableInterface;
use PSX\Test\TableDataSet;

/**
 * EntityAnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntityAnnotationTest extends DoctrineTestCase
{
	public function getDataSet()
	{
		$table = new Table($this->connection, 'psx_sql_table_test', array(
			'id'    => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
			'title' => TableInterface::TYPE_VARCHAR | 32,
			'date'  => TableInterface::TYPE_DATETIME,
		));

		$dataSet = new TableDataSet();
		$dataSet->addTable($table, array(
			array('id' => null, 'title' => 'foo', 'date' => date(DateTime::SQL)),
		));

		return $dataSet;
	}

	public function testGetTableDefinition()
	{
		$reader = new EntityAnnotation($this->getEntityManager());
		$table  = $reader->getTableDefinition('PSX\Sql\Table\Reader\EntityAnnotation\TestEntity');

		$this->assertEquals('bugs', $table->getName());

		$columns = $table->getColumns();

		$this->assertEquals(TableInterface::TYPE_INT | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT, $columns['id']);
		$this->assertEquals(TableInterface::TYPE_VARCHAR, $columns['description']);
		$this->assertEquals(TableInterface::TYPE_DATETIME, $columns['created']);
		$this->assertEquals(TableInterface::TYPE_VARCHAR, $columns['foobar']);
	}
}


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

namespace PSX\Data;

use PSX\Exception;

/**
 * ResultSetTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResultSetTest extends \PHPUnit_Framework_TestCase
{
	public function testFullResultSet()
	{
		$entries = array(
			array('id' => 1, 'title' => 'foo'),
			array('id' => 2, 'title' => 'bar'),
			array('id' => 3, 'title' => 'blu'),
			array('id' => 4, 'title' => 'bla'),
		);

		$resultSet = new ResultSet(12, 0, 2, $entries);

		$this->assertEquals(4, count($resultSet));
		$this->assertEquals(4, $resultSet->count());
		$this->assertEquals(12, $resultSet->getTotalResults());
		$this->assertEquals(0, $resultSet->getStartIndex());
		$this->assertEquals(2, $resultSet->getItemsPerPage());
		$this->assertEquals(false, $resultSet->isEmpty());

		foreach($resultSet as $i => $result)
		{
			$this->assertEquals($i + 1, $result['id']);
		}

		// test internal reset
		foreach($resultSet as $i => $result)
		{
			$this->assertEquals($i + 1, $result['id']);
		}
	}

	public function testEmptyResultSet()
	{
		$resultSet = new ResultSet(12, 0, 2, array());

		$this->assertEquals(0, count($resultSet));
		$this->assertEquals(0, $resultSet->count());
		$this->assertEquals(true, $resultSet->isEmpty());

		foreach($resultSet as $row)
		{
			throw new Exception('Should not happen');
		}
	}
}

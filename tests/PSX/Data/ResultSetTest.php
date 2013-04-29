<?php
/*
 *  $Id: ResultSetTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Data;

use PSX\Exception;

/**
 * PSX_Data_ResultSetTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class ResultSetTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

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
		$this->assertEquals(4, $resultSet->getLength());
		$this->assertEquals(true, $resultSet->hasResults());

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
		$this->assertEquals(0, $resultSet->getLength());
		$this->assertEquals(false, $resultSet->hasResults());

		foreach($resultSet as $row)
		{
			throw new Exception('Should not happen');
		}
	}
}

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

namespace PSX\Filter;

use PSX\Sql\DbTestCase;

/**
 * InColumnTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class InColumnTest extends DbTestCase
{
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../Handler/handler_fixture.xml');
	}

	public function testFilter()
	{
		$filter = new InColumn($this->connection, 'psx_handler_comment', 'id');

		$this->assertEquals(true, $filter->apply(1));
		$this->assertEquals(false, $filter->apply(32));

		// test error message
		$this->assertEquals('%s is not a valid value', $filter->getErrorMessage());
	}
}

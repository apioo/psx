<?php
/*
 *  $Id: BootstrapTest.php 528 2012-06-17 01:19:24Z k42b3.x@googlemail.com $
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

namespace PSX;

/**
 * PSX_BootstrapTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 528 $
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testSettings()
	{
		$this->assertEquals(true, defined('PSX_PATH_CACHE'));
		$this->assertEquals(true, defined('PSX_PATH_LIBRARY'));
		$this->assertEquals(true, defined('PSX_PATH_MODULE'));
		$this->assertEquals(true, defined('PSX_PATH_TEMPLATE'));
		$this->assertEquals(true, defined('PSX'));
		$this->assertEquals(true, strpos(get_include_path(), PSX_PATH_LIBRARY) !== false);
	}
}
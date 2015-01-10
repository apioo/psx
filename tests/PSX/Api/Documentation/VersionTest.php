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

namespace PSX\Api\Documentation;

use PSX\Api\View;

/**
 * VersionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
	public function testVersion()
	{
		$view1   = new View();
		$view2   = new View();
		$version = new Version('foo');

		$version->addView(1, $view1);
		$version->addView(2, $view2);

		$this->assertTrue($version->hasView(1));
		$this->assertTrue($version->hasView(2));
		$this->assertFalse($version->hasView(8));
		$this->assertEquals($view1, $version->getView(1));
		$this->assertEquals($view2, $version->getView(2));
		$this->assertEquals(null, $version->getView(8));
		$this->assertEquals(array(1 => $view1, 2 => $view2), $version->getViews());
		$this->assertEquals(2, $version->getLatestVersion());
		$this->assertTrue($version->isVersionRequired());
		$this->assertEquals('foo', $version->getDescription());
	}

	public function testGetLatestVersionNoView()
	{
		$version = new Version();

		$this->assertEquals(1, $version->getLatestVersion());
	}
}

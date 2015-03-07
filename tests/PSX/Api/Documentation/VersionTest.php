<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Api\Documentation;

use PSX\Api\View;

/**
 * VersionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

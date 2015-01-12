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

namespace PSX\Http;

/**
 * OptionsTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
	public function testOptions()
	{
		$callback = function(){};
		$options  = new Options();

		$options->setCallback($callback);
		$options->setTimeout(3);
		$options->setFollowLocation(true, 4);
		$options->setSsl(true);

		$this->assertEquals($callback, $options->getCallback());
		$this->assertEquals(3, $options->getTimeout());
		$this->assertEquals(true, $options->getFollowLocation());
		$this->assertEquals(4, $options->getMaxRedirects());
		$this->assertEquals(true, $options->getSsl());
	}
}

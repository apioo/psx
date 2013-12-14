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

namespace PSX;

/**
 * ContainerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testContainer()
	{
		$container = getContainer();

		$this->assertInstanceOf('\PSX\Base', $container->get('base'));
		$this->assertInstanceOf('\PSX\Config', $container->get('config'));
		$this->assertInstanceOf('\PSX\Dispatch', $container->get('dispatch'));
		$this->assertInstanceOf('\PSX\Http', $container->get('http'));
		$this->assertInstanceOf('\PSX\Input\Cookie', $container->get('inputCookie'));
		$this->assertInstanceOf('\PSX\Input\Files', $container->get('inputFiles'));
		$this->assertInstanceOf('\PSX\Input\Get', $container->get('inputGet'));
		$this->assertInstanceOf('\PSX\Input\Post', $container->get('inputPost'));
		$this->assertInstanceOf('\PSX\Input\Request', $container->get('inputRequest'));
		$this->assertInstanceOf('\PSX\Loader', $container->get('loader'));
		//$this->assertInstanceOf('\PSX\Session', $container->get('session'));
		//$this->assertInstanceOf('\PSX\Sql', $container->get('sql'));
		$this->assertInstanceOf('\PSX\Template', $container->get('template'));
		$this->assertInstanceOf('\PSX\Validate', $container->get('validate'));
	}
}


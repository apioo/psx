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

namespace PSX\Dependency;

/**
 * Check whether all default classes are available. We want fix this here becase
 * applications rely on these services
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DefaultContainerTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testGet()
	{
		$container = getContainer();

		$this->assertInstanceOf('PSX\Base', $container->get('base'));
		$this->assertInstanceOf('PSX\Config', $container->get('config'));
		$this->assertInstanceOf('PSX\Dispatch', $container->get('dispatch'));
		$this->assertInstanceOf('PSX\Http', $container->get('http'));
		$this->assertInstanceOf('PSX\Input\Cookie', $container->get('input_cookie'));
		$this->assertInstanceOf('PSX\Input\Files', $container->get('input_files'));
		$this->assertInstanceOf('PSX\Input\Get', $container->get('input_get'));
		$this->assertInstanceOf('PSX\Input\Post', $container->get('input_post'));
		$this->assertInstanceOf('PSX\Input\Request', $container->get('input_request'));
		$this->assertInstanceOf('PSX\Loader', $container->get('loader'));
		//$this->assertInstanceOf('PSX\Session', $container->get('session'));
		//$this->assertInstanceOf('PSX\Sql', $container->get('sql'));
		$this->assertInstanceOf('PSX\TemplateInterface', $container->get('template'));
		$this->assertInstanceOf('PSX\Validate', $container->get('validate'));
		$this->assertInstanceOf('PSX\Data\ReaderFactory', $container->get('reader_factory'));
		$this->assertInstanceOf('PSX\Data\WriterFactory', $container->get('writer_factory'));
		//$this->assertInstanceOf('PSX\Handler\HandlerManagerInterface', $container->get('database_manager'));
		$this->assertInstanceOf('PSX\Domain\DomainManagerInterface', $container->get('domain_manager'));
		$this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcherInterface', $container->get('event_dispatcher'));
	}
}

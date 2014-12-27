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
	public function testGet()
	{
		$container = getContainer();

		// command
		$this->assertInstanceOf('PSX\Dispatch\CommandFactoryInterface', $container->get('command_factory'));
		$this->assertInstanceOf('PSX\Command\OutputInterface', $container->get('command_output'));
		$this->assertInstanceOf('PSX\Command\Executor', $container->get('executor'));

		// console
		$this->assertInstanceOf('Symfony\Component\Console\Application', $container->get('console'));
		$this->assertInstanceOf('PSX\Console\ReaderInterface', $container->get('console_reader'));

		// controller
		$this->assertInstanceOf('PSX\Dispatch\ControllerFactoryInterface', $container->get('application_stack_factory'));
		$this->assertInstanceOf('PSX\Dispatch\ControllerFactoryInterface', $container->get('controller_factory'));
		$this->assertInstanceOf('PSX\Dispatch\SenderInterface', $container->get('dispatch_sender'));
		$this->assertInstanceOf('PSX\Loader\LocationFinderInterface', $container->get('loader_location_finder'));
		$this->assertInstanceOf('PSX\Loader\CallbackResolverInterface', $container->get('loader_callback_resolver'));
		$this->assertInstanceOf('PSX\Loader', $container->get('loader'));
		$this->assertInstanceOf('PSX\Dispatch\RequestFactoryInterface', $container->get('request_factory'));
		$this->assertInstanceOf('PSX\Dispatch\ResponseFactoryInterface', $container->get('response_factory'));
		$this->assertInstanceOf('PSX\Dispatch', $container->get('dispatch'));
		$this->assertInstanceOf('PSX\Loader\RoutingParserInterface', $container->get('routing_parser'));
		$this->assertInstanceOf('PSX\Loader\ReverseRouter', $container->get('reverse_router'));

		// data
		$this->assertInstanceOf('PSX\Data\ReaderFactory', $container->get('reader_factory'));
		$this->assertInstanceOf('PSX\Data\WriterFactory', $container->get('writer_factory'));
		$this->assertInstanceOf('PSX\Data\Transformer\TransformerManager', $container->get('transformer_manager'));
		$this->assertInstanceOf('PSX\Data\Record\ImporterManager', $container->get('importer_manager'));
		$this->assertInstanceOf('PSX\Data\Schema\SchemaManagerInterface', $container->get('schema_manager'));
		$this->assertInstanceOf('PSX\Data\Schema\ValidatorInterface', $container->get('schema_validator'));
		$this->assertInstanceOf('PSX\Data\Schema\Assimilator', $container->get('schema_assimilator'));
		$this->assertInstanceOf('PSX\Data\Record\FactoryFactory', $container->get('record_factory_factory'));
		$this->assertInstanceOf('PSX\Data\Importer', $container->get('importer'));
		$this->assertInstanceOf('PSX\Data\Extractor', $container->get('extractor'));
		$this->assertInstanceOf('PSX\Data\SerializerInterface', $container->get('serializer'));

		// default
		$this->assertInstanceOf('PSX\Base', $container->get('base'));
		$this->assertInstanceOf('PSX\Config', $container->get('config'));
		$this->assertInstanceOf('PSX\Http', $container->get('http'));
		$this->assertInstanceOf('PSX\Session', $container->get('session'));
		
		if(defined('PSX_CONNECTION') && PSX_CONNECTION === true)
		{
			$this->assertInstanceOf('Doctrine\DBAL\Connection', $container->get('connection'));
			$this->assertInstanceOf('PSX\Sql\TableManager', $container->get('table_manager'));
		}

		$this->assertInstanceOf('PSX\TemplateInterface', $container->get('template'));
		$this->assertInstanceOf('PSX\Validate', $container->get('validate'));
		$this->assertInstanceOf('PSX\Dependency\ObjectBuilderInterface', $container->get('object_builder'));
		$this->assertInstanceOf('Psr\Cache\CacheItemPoolInterface', $container->get('cache'));
		$this->assertInstanceOf('Psr\Log\LoggerInterface', $container->get('logger'));

		// event
		$this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcherInterface', $container->get('event_dispatcher'));
	}
}

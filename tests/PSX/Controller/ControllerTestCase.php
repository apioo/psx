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

namespace PSX\Controller;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use PDOException;
use PSX\Dispatch\VoidSender;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Loader;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;
use PSX\Loader\RoutingParser;
use PSX\Loader\InvalidPathException;
use PSX\Url;
use ReflectionClass;

/**
 * ControllerTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ControllerTestCase extends \PHPUnit_Extensions_Database_TestCase
{
	protected static $con;

	protected $paths;
	protected $connection;

	protected $containerBackup;

	public function getConnection()
	{
		if(self::$con === null)
		{
			try
			{
				self::$con = getContainer()->get('connection');
			}
			catch(PDOException $e)
			{
				$this->markTestSkipped($e->getMessage());
			}
		}

		if($this->connection === null)
		{
			$this->connection = self::$con;
		}

		return $this->createDefaultDBConnection($this->connection->getWrappedConnection(), getContainer()->get('config')->get('psx_sql_db'));
	}

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../Handler/handler_fixture.xml');
	}

	protected function setUp()
	{
		parent::setUp();

		// we remove all used services so that our test has no side effects
		$serviceIds = getContainer()->getServiceIds();
		foreach($serviceIds as $serviceId)
		{
			getContainer()->set($serviceId, null);
		}

		// we replace the routing parser
		getContainer()->set('routing_parser', new RoutingParser\ArrayCollection($this->getPaths()));

		// assign the phpunit test case
		getContainer()->set('test_case', $this);

		// use void sender
		getContainer()->set('dispatch_sender', new VoidSender());

		// enables us to load the same controller method multiple times
		getContainer()->get('loader')->setRecursiveLoading(true);

		// use void sender
		getContainer()->set('dispatch_sender', new VoidSender());

		// set void logger
		$logger = new Logger('psx');
		$logger->pushHandler(new NullHandler());

		getContainer()->set('logger', $logger);
	}

	protected function tearDown()
	{
		parent::tearDown();

		// we remove all used services so that our test has no side effects
		$serviceIds = getContainer()->getServiceIds();
		foreach($serviceIds as $serviceId)
		{
			getContainer()->set($serviceId, null);
		}
	}

	/**
	 * Loads an specific controller
	 *
	 * @param PSX\Http\Request $request
	 * @param PSX\Http\Response $response
	 * @return PSX\ControllerAbstract
	 */
	protected function loadController(Request $request, Response $response)
	{
		return getContainer()->get('dispatch')->route($request, $response);
	}

	/**
	 * Returns the available modules for the testcase
	 *
	 * @return array
	 */
	abstract protected function getPaths();
}

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

use PDOException;
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
	protected $sql;

	public function getConnection()
	{
		if(self::$con === null)
		{
			try
			{
				self::$con = getContainer()->get('sql');
			}
			catch(PDOException $e)
			{
				$this->markTestSkipped($e->getMessage());
			}
		}

		if($this->sql === null)
		{
			$this->sql = self::$con;
		}

		return $this->createDefaultDBConnection($this->sql, getContainer()->get('config')->get('psx_sql_db'));
	}

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../Handler/handler_fixture.xml');
	}

	protected function setUp()
	{
		parent::setUp();

		// we replace the routing parser
		getContainer()->set('routing_parser', new RoutingParser\ArrayCollection($this->getPaths()));

		// we must delete the cache of some services so that they take the new 
		// routing parser
		getContainer()->set('loader_location_finder', null);
		getContainer()->set('loader', null);
		getContainer()->set('reverse_router', null);

		// assign the phpunit test case
		getContainer()->set('test_case', $this);

		// enables us to load the same controller method multiple times
		getContainer()->get('loader')->setRecursiveLoading(true);
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Loads an specific controller
	 *
	 * @param string path
	 * @return PSX\ModuleAbstract
	 */
	protected function loadController(Request $request, Response $response)
	{
		return getContainer()->get('loader')->load($request, $response);
	}

	/**
	 * Returns the available modules for the testcase
	 *
	 * @return array
	 */
	abstract protected function getPaths();
}
